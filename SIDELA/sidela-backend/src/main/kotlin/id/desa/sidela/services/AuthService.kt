package id.desa.sidela.services

import id.desa.sidela.database.*
import id.desa.sidela.models.*
import id.desa.sidela.utils.JwtConfig
import id.desa.sidela.utils.PasswordHasher
import org.jetbrains.exposed.sql.*
import java.time.LocalDateTime

object AuthService {

    suspend fun login(req: LoginRequest): AuthResponse? {
        return DatabaseFactory.dbQuery {
            val userRow = UsersTable
                .innerJoin(RolesTable, { roleId }, { id })
                .select {
                    ((UsersTable.username eq req.username) or (UsersTable.email eq req.username)) and
                    (UsersTable.isActive eq true)
                }
                .singleOrNull() ?: return@dbQuery null

            val storedPasswordHash = userRow[UsersTable.passwordHash]
            if (!PasswordHasher.verify(req.password, storedPasswordHash)) {
                return@dbQuery null
            }

            val userId = userRow[UsersTable.id]
            val username = userRow[UsersTable.username]
            val roleSlug = userRow[RolesTable.slug]

            // Update last login
            UsersTable.update({ UsersTable.id eq userId }) {
                it[lastLoginAt] = LocalDateTime.now()
            }

            val token = JwtConfig.generateToken(userId, username, roleSlug)
            val userProfile = buildUserProfile(userRow)

            AuthResponse(
                token = token,
                expiresInSeconds = JwtConfig.getExpirationSeconds(),
                user = userProfile
            )
        }
    }

    suspend fun getUserProfile(userId: Long): UserProfileDTO? {
        return DatabaseFactory.dbQuery {
            val userRow = UsersTable
                .innerJoin(RolesTable, { roleId }, { id })
                .select { UsersTable.id eq userId }
                .singleOrNull() ?: return@dbQuery null

            buildUserProfile(userRow)
        }
    }

    suspend fun changePassword(userId: Long, req: ChangePasswordRequest): Boolean {
        return DatabaseFactory.dbQuery {
            val user = UsersTable.select { UsersTable.id eq userId }.singleOrNull() ?: return@dbQuery false
            val currentHash = user[UsersTable.passwordHash]

            if (!PasswordHasher.verify(req.oldPassword, currentHash)) {
                return@dbQuery false
            }

            val newHash = PasswordHasher.hash(req.newPassword)
            UsersTable.update({ UsersTable.id eq userId }) {
                it[passwordHash] = newHash
                it[updatedAt] = LocalDateTime.now()
            } > 0
        }
    }

    suspend fun listUsers(): List<UserProfileDTO> {
        return DatabaseFactory.dbQuery {
            UsersTable
                .innerJoin(RolesTable, { roleId }, { id })
                .select { UsersTable.isActive eq true }
                .map { buildUserProfile(it) }
        }
    }

    private fun buildUserProfile(row: ResultRow): UserProfileDTO {
        val uId = row[UsersTable.id]
        val role = RoleDTO(
            id = row[RolesTable.id],
            name = row[RolesTable.name],
            slug = row[RolesTable.slug],
            description = row[RolesTable.description]
        )

        val perangkatRow = PerangkatDesaTable
            .select { PerangkatDesaTable.userId eq uId }
            .singleOrNull()

        val perangkatDto = perangkatRow?.let {
            val dusunName = it[PerangkatDesaTable.dusunId]?.let { dId ->
                HamletsTable.select { HamletsTable.id eq dId }.singleOrNull()?.get(HamletsTable.name)
            }

            PerangkatDesaDTO(
                id = it[PerangkatDesaTable.id],
                nipd = it[PerangkatDesaTable.nipd],
                jabatan = it[PerangkatDesaTable.jabatan],
                dusunId = it[PerangkatDesaTable.dusunId],
                dusunName = dusunName,
                alamat = it[PerangkatDesaTable.alamat],
                tanggalSk = it[PerangkatDesaTable.tanggalSk]?.toString(),
                nomorSk = it[PerangkatDesaTable.nomorSk],
                statusKeaktifan = it[PerangkatDesaTable.statusKeaktifan]
            )
        }

        val faceProfile = FaceProfilesTable.select {
            (FaceProfilesTable.userId eq uId) and (FaceProfilesTable.isEnrolled eq true)
        }.singleOrNull()

        return UserProfileDTO(
            id = uId,
            username = row[UsersTable.username],
            email = row[UsersTable.email],
            fullName = row[UsersTable.fullName],
            nik = row[UsersTable.nik],
            phone = row[UsersTable.phone],
            avatarUrl = row[UsersTable.avatarUrl] ?: "https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400",
            role = role,
            perangkatDesa = perangkatDto,
            isFaceEnrolled = faceProfile != null
        )
    }
}
