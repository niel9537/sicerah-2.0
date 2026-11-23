package id.desa.sidela.models

import kotlinx.serialization.Serializable

@Serializable
data class LoginRequest(
    val username: String,
    val password: String
)

@Serializable
data class AuthResponse(
    val token: String,
    val tokenType: String = "Bearer",
    val expiresInSeconds: Long,
    val user: UserProfileDTO
)

@Serializable
data class UserProfileDTO(
    val id: Long,
    val username: String,
    val email: String,
    val fullName: String,
    val nik: String,
    val phone: String? = null,
    val avatarUrl: String? = null,
    val role: RoleDTO,
    val perangkatDesa: PerangkatDesaDTO? = null,
    val isFaceEnrolled: Boolean = false
)

@Serializable
data class RoleDTO(
    val id: Int,
    val name: String,
    val slug: String,
    val description: String? = null
)

@Serializable
data class PerangkatDesaDTO(
    val id: Long,
    val nipd: String? = null,
    val jabatan: String,
    val dusunId: Int? = null,
    val dusunName: String? = null,
    val alamat: String? = null,
    val tanggalSk: String? = null,
    val nomorSk: String? = null,
    val statusKeaktifan: String
)

@Serializable
data class ChangePasswordRequest(
    val oldPassword: String,
    val newPassword: String
)

@Serializable
data class ResetPasswordRequest(
    val emailOrUsername: String
)

@Serializable
data class CreateUserRequest(
    val username: String,
    val email: String,
    val password: String,
    val fullName: String,
    val nik: String,
    val phone: String? = null,
    val roleId: Int,
    val jabatan: String,
    val dusunId: Int? = null,
    val nipd: String? = null
)

@Serializable
data class UpdateUserRequest(
    val fullName: String? = null,
    val email: String? = null,
    val phone: String? = null,
    val jabatan: String? = null,
    val dusunId: Int? = null,
    val isActive: Boolean? = null
)
