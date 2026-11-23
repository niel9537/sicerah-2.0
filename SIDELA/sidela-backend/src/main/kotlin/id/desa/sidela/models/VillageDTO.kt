package id.desa.sidela.models

import kotlinx.serialization.Serializable

@Serializable
data class VillageProfileDTO(
    val id: Int = 1,
    val villageName: String,
    val district: String,
    val regency: String,
    val province: String,
    val postalCode: String,
    val address: String,
    val phone: String? = null,
    val email: String? = null,
    val website: String? = null,
    val logoUrl: String? = null,
    val vision: String? = null,
    val mission: String? = null,
    val organizationalStructureUrl: String? = null
)

@Serializable
data class ResidentDTO(
    val id: Long,
    val familyId: Long? = null,
    val noKk: String? = null,
    val hamletId: Int,
    val hamletName: String,
    val nik: String,
    val namaLengkap: String,
    val jenisKelamin: String,
    val tempatLahir: String,
    val tanggalLahir: String,
    val agama: String,
    val pendidikan: String,
    val pekerjaan: String,
    val statusPerkawinan: String,
    val hubunganKeluarga: String,
    val statusHidup: String
)

@Serializable
data class ResidentInput(
    val familyId: Long? = null,
    val hamletId: Int,
    val nik: String,
    val namaLengkap: String,
    val jenisKelamin: String,
    val tempatLahir: String,
    val tanggalLahir: String,
    val agama: String = "KRISTEN_PROTESTAN",
    val pendidikan: String = "SLTA/SEDERAJAT",
    val pekerjaan: String = "PETANI/PEKEBUN",
    val statusPerkawinan: String = "BELUM_KAWIN",
    val hubunganKeluarga: String = "KEPALA_KELUARGA",
    val statusHidup: String = "HIDUP"
)

@Serializable
data class HamletDTO(
    val id: Int,
    val name: String,
    val code: String,
    val headOfHamlet: String? = null,
    val areaSqkm: Double = 0.0,
    val populationCount: Int = 0
)

@Serializable
data class VillageAssetDTO(
    val id: Long,
    val assetCode: String,
    val assetName: String,
    val category: String,
    val acquisitionYear: Int,
    val sourceOfFunds: String,
    val acquisitionCost: Double,
    val quantity: Int,
    val unit: String,
    val condition: String,
    val location: String,
    val personInCharge: String,
    val photoUrl: String? = null,
    val notes: String? = null
)

@Serializable
data class VillageAssetInput(
    val assetCode: String,
    val assetName: String,
    val category: String,
    val acquisitionYear: Int,
    val sourceOfFunds: String = "APBDes / Dana Desa",
    val acquisitionCost: Double = 0.0,
    val quantity: Int = 1,
    val unit: String = "Unit",
    val condition: String = "BAIK",
    val location: String,
    val personInCharge: String,
    val photoUrl: String? = null,
    val notes: String? = null
)

@Serializable
data class LetterArchiveDTO(
    val id: Long,
    val type: String, // INCOMING or OUTGOING
    val letterNumber: String,
    val letterDate: String,
    val senderOrRecipient: String,
    val subject: String,
    val summary: String? = null,
    val fileUrl: String? = null,
    val status: String
)

@Serializable
data class LetterArchiveInput(
    val type: String,
    val letterNumber: String,
    val letterDate: String,
    val receivedDate: String? = null,
    val senderOrRecipient: String,
    val subject: String,
    val summary: String? = null,
    val fileUrl: String? = null,
    val dispositionTo: Long? = null,
    val dispositionNotes: String? = null,
    val status: String = "TERKIRIM"
)
