package id.desa.sidela.services

import id.desa.sidela.database.*
import id.desa.sidela.models.*
import org.jetbrains.exposed.sql.*
import java.math.BigDecimal
import java.time.LocalDate

object VillageService {

    suspend fun getVillageProfile(): VillageProfileDTO {
        return DatabaseFactory.dbQuery {
            val row = VillageProfilesTable.selectAll().singleOrNull()
            if (row != null) {
                VillageProfileDTO(
                    id = row[VillageProfilesTable.id],
                    villageName = row[VillageProfilesTable.villageName],
                    district = row[VillageProfilesTable.district],
                    regency = row[VillageProfilesTable.regency],
                    province = row[VillageProfilesTable.province],
                    postalCode = row[VillageProfilesTable.postalCode],
                    address = row[VillageProfilesTable.address],
                    phone = row[VillageProfilesTable.phone],
                    email = row[VillageProfilesTable.email],
                    website = row[VillageProfilesTable.website],
                    logoUrl = row[VillageProfilesTable.logoUrl],
                    vision = row[VillageProfilesTable.vision],
                    mission = row[VillageProfilesTable.mission]
                )
            } else {
                VillageProfileDTO(
                    id = 1,
                    villageName = "Iraonogela",
                    district = "Mandrehe",
                    regency = "Nias Barat",
                    province = "Sumatera Utara",
                    postalCode = "22862",
                    address = "Jalan Poros Desa Iraonogela, Kec. Mandrehe, Kab. Nias Barat"
                )
            }
        }
    }

    suspend fun getHamlets(): List<HamletDTO> {
        return DatabaseFactory.dbQuery {
            HamletsTable.selectAll().map {
                val headName = it[HamletsTable.headOfHamletUserId]?.let { uId ->
                    UsersTable.select { UsersTable.id eq uId }.singleOrNull()?.get(UsersTable.fullName)
                }
                HamletDTO(
                    id = it[HamletsTable.id],
                    name = it[HamletsTable.name],
                    code = it[HamletsTable.code],
                    headOfHamlet = headName,
                    areaSqkm = it[HamletsTable.areaSqkm].toDouble(),
                    populationCount = it[HamletsTable.populationCount]
                )
            }
        }
    }

    suspend fun getResidents(search: String? = null, hamletId: Int? = null, page: Int = 1, size: Int = 20): PaginatedData<ResidentDTO> {
        return DatabaseFactory.dbQuery {
            var query = ResidentsTable.selectAll()

            if (!search.isNullOrBlank()) {
                query = query.andWhere {
                    (ResidentsTable.namaLengkap like "%$search%") or (ResidentsTable.nik like "%$search%")
                }
            }

            if (hamletId != null) {
                query = query.andWhere { ResidentsTable.hamletId eq hamletId }
            }

            val totalCount = query.count()
            val totalPages = ((totalCount + size - 1) / size).toInt()
            val offset = ((page - 1) * size).toLong()

            val items = query.limit(size, offset = offset).map {
                val hName = HamletsTable.select { HamletsTable.id eq it[ResidentsTable.hamletId] }.singleOrNull()?.get(HamletsTable.name) ?: "Dusun I"
                val famNoKk = it[ResidentsTable.familyId]?.let { fId ->
                    FamiliesTable.select { FamiliesTable.id eq fId }.singleOrNull()?.get(FamiliesTable.noKk)
                }

                ResidentDTO(
                    id = it[ResidentsTable.id],
                    familyId = it[ResidentsTable.familyId],
                    noKk = famNoKk,
                    hamletId = it[ResidentsTable.hamletId],
                    hamletName = hName,
                    nik = it[ResidentsTable.nik],
                    namaLengkap = it[ResidentsTable.namaLengkap],
                    jenisKelamin = it[ResidentsTable.jenisKelamin],
                    tempatLahir = it[ResidentsTable.tempatLahir],
                    tanggalLahir = it[ResidentsTable.tanggalLahir].toString(),
                    agama = it[ResidentsTable.agama],
                    pendidikan = it[ResidentsTable.pendidikan],
                    pekerjaan = it[ResidentsTable.pekerjaan],
                    statusPerkawinan = it[ResidentsTable.statusPerkawinan],
                    hubunganKeluarga = it[ResidentsTable.hubunganKeluarga],
                    statusHidup = it[ResidentsTable.statusHidup]
                )
            }

            PaginatedData(
                items = items,
                totalCount = totalCount,
                page = page,
                pageSize = size,
                totalPages = totalPages
            )
        }
    }

    suspend fun createResident(input: ResidentInput): ResidentDTO {
        return DatabaseFactory.dbQuery {
            val insertedId = ResidentsTable.insert {
                it[familyId] = input.familyId
                it[hamletId] = input.hamletId
                it[nik] = input.nik
                it[namaLengkap] = input.namaLengkap
                it[jenisKelamin] = input.jenisKelamin
                it[tempatLahir] = input.tempatLahir
                it[tanggalLahir] = LocalDate.parse(input.tanggalLahir)
                it[agama] = input.agama
                it[pendidikan] = input.pendidikan
                it[pekerjaan] = input.pekerjaan
                it[statusPerkawinan] = input.statusPerkawinan
                it[hubunganKeluarga] = input.hubunganKeluarga
                it[statusHidup] = input.statusHidup
            } get ResidentsTable.id

            getResidents(search = input.nik).items.first()
        }
    }

    suspend fun getAssets(): List<VillageAssetDTO> {
        return DatabaseFactory.dbQuery {
            VillageAssetsTable.selectAll().map {
                VillageAssetDTO(
                    id = it[VillageAssetsTable.id],
                    assetCode = it[VillageAssetsTable.assetCode],
                    assetName = it[VillageAssetsTable.assetName],
                    category = it[VillageAssetsTable.category],
                    acquisitionYear = it[VillageAssetsTable.acquisitionYear],
                    sourceOfFunds = it[VillageAssetsTable.sourceOfFunds],
                    acquisitionCost = it[VillageAssetsTable.acquisitionCost].toDouble(),
                    quantity = it[VillageAssetsTable.quantity],
                    unit = it[VillageAssetsTable.unit],
                    condition = it[VillageAssetsTable.condition],
                    location = it[VillageAssetsTable.location],
                    personInCharge = it[VillageAssetsTable.personInCharge],
                    photoUrl = it[VillageAssetsTable.photoUrl],
                    notes = it[VillageAssetsTable.notes]
                )
            }
        }
    }

    suspend fun getLetters(type: String? = null): List<LetterArchiveDTO> {
        return DatabaseFactory.dbQuery {
            val list = mutableListOf<LetterArchiveDTO>()

            if (type == null || type.equals("INCOMING", ignoreCase = true)) {
                IncomingLettersTable.selectAll().forEach {
                    list.add(
                        LetterArchiveDTO(
                            id = it[IncomingLettersTable.id],
                            type = "INCOMING",
                            letterNumber = it[IncomingLettersTable.letterNumber],
                            letterDate = it[IncomingLettersTable.letterDate].toString(),
                            senderOrRecipient = it[IncomingLettersTable.sender],
                            subject = it[IncomingLettersTable.subject],
                            summary = it[IncomingLettersTable.summary],
                            fileUrl = it[IncomingLettersTable.fileUrl],
                            status = it[IncomingLettersTable.status]
                        )
                    )
                }
            }

            if (type == null || type.equals("OUTGOING", ignoreCase = true)) {
                OutgoingLettersTable.selectAll().forEach {
                    list.add(
                        LetterArchiveDTO(
                            id = it[OutgoingLettersTable.id],
                            type = "OUTGOING",
                            letterNumber = it[OutgoingLettersTable.letterNumber],
                            letterDate = it[OutgoingLettersTable.letterDate].toString(),
                            senderOrRecipient = it[OutgoingLettersTable.recipient],
                            subject = it[OutgoingLettersTable.subject],
                            summary = it[OutgoingLettersTable.summary],
                            fileUrl = it[OutgoingLettersTable.fileUrl],
                            status = it[OutgoingLettersTable.status]
                        )
                    )
                }
            }

            list.sortedByDescending { it.letterDate }
        }
    }
}
