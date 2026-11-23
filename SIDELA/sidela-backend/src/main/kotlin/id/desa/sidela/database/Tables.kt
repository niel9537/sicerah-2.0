package id.desa.sidela.database

import org.jetbrains.exposed.sql.Table
import org.jetbrains.exposed.sql.javatime.date
import org.jetbrains.exposed.sql.javatime.datetime
import org.jetbrains.exposed.sql.javatime.time

object RolesTable : Table("roles") {
    val id = integer("id").autoIncrement()
    val name = varchar("name", 50)
    val slug = varchar("slug", 50)
    val description = varchar("description", 255).nullable()
    override val primaryKey = PrimaryKey(id)
}

object UsersTable : Table("users") {
    val id = long("id").autoIncrement()
    val roleId = integer("role_id").references(RolesTable.id)
    val username = varchar("username", 60)
    val email = varchar("email", 100)
    val passwordHash = varchar("password_hash", 255)
    val fullName = varchar("full_name", 120)
    val nik = varchar("nik", 16)
    val phone = varchar("phone", 20).nullable()
    val avatarUrl = varchar("avatar_url", 255).nullable()
    val isActive = bool("is_active").default(true)
    val lastLoginAt = datetime("last_login_at").nullable()
    val createdAt = datetime("created_at")
    val updatedAt = datetime("updated_at")
    val deletedAt = datetime("deleted_at").nullable()
    override val primaryKey = PrimaryKey(id)
}

object HamletsTable : Table("hamlets") {
    val id = integer("id").autoIncrement()
    val name = varchar("name", 100)
    val code = varchar("code", 20)
    val headOfHamletUserId = long("head_of_hamlet_user_id").nullable()
    val areaSqkm = decimal("area_sqkm", 6, 2).default(0.00.toBigDecimal())
    val populationCount = integer("population_count").default(0)
    override val primaryKey = PrimaryKey(id)
}

object PerangkatDesaTable : Table("perangkat_desa") {
    val id = long("id").autoIncrement()
    val userId = long("user_id").references(UsersTable.id)
    val nipd = varchar("nipd", 30).nullable()
    val jabatan = varchar("jabatan", 100)
    val dusunId = integer("dusun_id").nullable()
    val alamat = text("alamat").nullable()
    val tanggalSk = date("tanggal_sk").nullable()
    val nomorSk = varchar("nomor_sk", 100).nullable()
    val statusKeaktifan = varchar("status_keaktifan", 20).default("AKTIF")
    override val primaryKey = PrimaryKey(id)
}

object FaceProfilesTable : Table("face_profiles") {
    val id = long("id").autoIncrement()
    val userId = long("user_id").references(UsersTable.id)
    val faceEmbedding = text("face_embedding")
    val faceImageUrl = varchar("face_image_url", 255)
    val isEnrolled = bool("is_enrolled").default(true)
    val enrolledAt = datetime("enrolled_at")
    val lastVerifiedAt = datetime("last_verified_at").nullable()
    val modelVersion = varchar("model_version", 50).default("MobileFaceNet-V1-MLKit")
    val resetCount = integer("reset_count").default(0)
    val isActive = bool("is_active").default(true)
    override val primaryKey = PrimaryKey(id)
}

object AttendanceSettingsTable : Table("attendance_settings") {
    val id = integer("id").autoIncrement()
    val officeName = varchar("office_name", 150)
    val latitude = decimal("latitude", 10, 7)
    val longitude = decimal("longitude", 10, 7)
    val radiusMeters = integer("radius_meters")
    val workStartTime = time("work_start_time")
    val workEndTime = time("work_end_time")
    val lateToleranceMinutes = integer("late_tolerance_minutes")
    val breakStartTime = time("break_start_time")
    val breakEndTime = time("break_end_time")
    val isActive = bool("is_active").default(true)
    override val primaryKey = PrimaryKey(id)
}

object AttendanceTable : Table("attendance") {
    val id = long("id").autoIncrement()
    val userId = long("user_id").references(UsersTable.id)
    val date = date("date")
    val clockIn = time("clock_in").nullable()
    val clockOut = time("clock_out").nullable()
    val clockInLat = decimal("clock_in_lat", 10, 7).nullable()
    val clockInLon = decimal("clock_in_lon", 10, 7).nullable()
    val clockInDistanceMeters = decimal("clock_in_distance_meters", 8, 2).nullable()
    val clockInPhotoUrl = varchar("clock_in_photo_url", 255).nullable()
    val clockOutLat = decimal("clock_out_lat", 10, 7).nullable()
    val clockOutLon = decimal("clock_out_lon", 10, 7).nullable()
    val clockOutDistanceMeters = decimal("clock_out_distance_meters", 8, 2).nullable()
    val clockOutPhotoUrl = varchar("clock_out_photo_url", 255).nullable()
    val faceConfidenceIn = decimal("face_confidence_in", 5, 2).nullable()
    val faceConfidenceOut = decimal("face_confidence_out", 5, 2).nullable()
    val livenessPassedIn = bool("liveness_passed_in").default(true)
    val livenessPassedOut = bool("liveness_passed_out").default(true)
    val status = varchar("status", 20).default("ALPHA")
    val lateMinutes = integer("late_minutes").default(0)
    val earlyLeaveMinutes = integer("early_leave_minutes").default(0)
    val totalWorkMinutes = integer("total_work_minutes").default(0)
    val verificationMethod = varchar("verification_method", 30).default("FACE_RECOGNITION")
    val notes = text("notes").nullable()
    override val primaryKey = PrimaryKey(id)
}

object LeaveRequestsTable : Table("leave_requests") {
    val id = long("id").autoIncrement()
    val userId = long("user_id").references(UsersTable.id)
    val leaveType = varchar("leave_type", 30)
    val startDate = date("start_date")
    val endDate = date("end_date")
    val totalDays = integer("total_days").default(1)
    val reason = text("reason")
    val attachmentUrl = varchar("attachment_url", 255).nullable()
    val destinationLocation = varchar("destination_location", 150).nullable()
    val officialDutyNumber = varchar("official_duty_number", 100).nullable()
    val status = varchar("status", 20).default("PENDING")
    val approvedBy = long("approved_by").nullable()
    val approvedAt = datetime("approved_at").nullable()
    val rejectionNotes = text("rejection_notes").nullable()
    val createdAt = datetime("created_at")
    override val primaryKey = PrimaryKey(id)
}

object WorkJournalsTable : Table("work_journals") {
    val id = long("id").autoIncrement()
    val userId = long("user_id").references(UsersTable.id)
    val date = date("date")
    val title = varchar("title", 200)
    val description = text("description")
    val startTime = time("start_time")
    val endTime = time("end_time")
    val workOutput = varchar("work_output", 255)
    val status = varchar("status", 20).default("ON_PROGRESS")
    val feedbackNotes = text("feedback_notes").nullable()
    val verifiedBy = long("verified_by").nullable()
    val verifiedAt = datetime("verified_at").nullable()
    val createdAt = datetime("created_at")
    override val primaryKey = PrimaryKey(id)
}

object JournalAttachmentsTable : Table("journal_attachments") {
    val id = long("id").autoIncrement()
    val journalId = long("journal_id").references(WorkJournalsTable.id)
    val fileName = varchar("file_name", 255)
    val fileUrl = varchar("file_url", 255)
    val fileType = varchar("file_type", 50)
    val fileSizeBytes = long("file_size_bytes").default(0)
    override val primaryKey = PrimaryKey(id)
}

object VillageProfilesTable : Table("village_profiles") {
    val id = integer("id")
    val villageName = varchar("village_name", 100)
    val district = varchar("district", 100)
    val regency = varchar("regency", 100)
    val province = varchar("province", 100)
    val postalCode = varchar("postal_code", 10)
    val address = text("address")
    val phone = varchar("phone", 25).nullable()
    val email = varchar("email", 100).nullable()
    val website = varchar("website", 150).nullable()
    val logoUrl = varchar("logo_url", 255).nullable()
    val vision = text("vision").nullable()
    val mission = text("mission").nullable()
    override val primaryKey = PrimaryKey(id)
}

object FamiliesTable : Table("families") {
    val id = long("id").autoIncrement()
    val noKk = varchar("no_kk", 16)
    val headOfFamilyNik = varchar("head_of_family_nik", 16)
    val headOfFamilyName = varchar("head_of_family_name", 120)
    val hamletId = integer("hamlet_id")
    val address = text("address")
    override val primaryKey = PrimaryKey(id)
}

object ResidentsTable : Table("residents") {
    val id = long("id").autoIncrement()
    val familyId = long("family_id").nullable()
    val hamletId = integer("hamlet_id")
    val nik = varchar("nik", 16)
    val namaLengkap = varchar("nama_lengkap", 150)
    val jenisKelamin = varchar("jenis_kelamin", 2)
    val tempatLahir = varchar("tempat_lahir", 100)
    val tanggalLahir = date("tanggal_lahir")
    val agama = varchar("agama", 30)
    val pendidikan = varchar("pendidikan", 50)
    val pekerjaan = varchar("pekerjaan", 100)
    val statusPerkawinan = varchar("status_perkawinan", 30)
    val hubunganKeluarga = varchar("hubungan_keluarga", 30)
    val statusHidup = varchar("status_hidup", 20).default("HIDUP")
    override val primaryKey = PrimaryKey(id)
}

object VillageAssetsTable : Table("village_assets") {
    val id = long("id").autoIncrement()
    val assetCode = varchar("asset_code", 50)
    val assetName = varchar("asset_name", 200)
    val category = varchar("category", 30)
    val acquisitionYear = integer("acquisition_year")
    val sourceOfFunds = varchar("source_of_funds", 100)
    val acquisitionCost = decimal("acquisition_cost", 15, 2)
    val quantity = integer("quantity")
    val unit = varchar("unit", 30)
    val condition = varchar("condition", 20)
    val location = varchar("location", 150)
    val personInCharge = varchar("person_in_charge", 120)
    val photoUrl = varchar("photo_url", 255).nullable()
    val notes = text("notes").nullable()
    override val primaryKey = PrimaryKey(id)
}

object IncomingLettersTable : Table("incoming_letters") {
    val id = long("id").autoIncrement()
    val letterNumber = varchar("letter_number", 100)
    val letterDate = date("letter_date")
    val receivedDate = date("received_date")
    val sender = varchar("sender", 150)
    val subject = varchar("subject", 255)
    val summary = text("summary").nullable()
    val fileUrl = varchar("file_url", 255).nullable()
    val status = varchar("status", 30).default("DITERIMA")
    override val primaryKey = PrimaryKey(id)
}

object OutgoingLettersTable : Table("outgoing_letters") {
    val id = long("id").autoIncrement()
    val letterNumber = varchar("letter_number", 100)
    val letterDate = date("letter_date")
    val recipient = varchar("recipient", 150)
    val subject = varchar("subject", 255)
    val summary = text("summary").nullable()
    val fileUrl = varchar("file_url", 255).nullable()
    val status = varchar("status", 30).default("TERKIRIM")
    override val primaryKey = PrimaryKey(id)
}

object NotificationsTable : Table("notifications") {
    val id = long("id").autoIncrement()
    val userId = long("user_id").references(UsersTable.id)
    val title = varchar("title", 150)
    val message = text("message")
    val type = varchar("type", 30)
    val isRead = bool("is_read").default(false)
    val createdAt = datetime("created_at")
    override val primaryKey = PrimaryKey(id)
}

object AuditLogsTable : Table("audit_logs") {
    val id = long("id").autoIncrement()
    val userId = long("user_id").references(UsersTable.id).nullable()
    val action = varchar("action", 100)
    val ipAddress = varchar("ip_address", 45).nullable()
    val details = text("details").nullable()
    val createdAt = datetime("created_at")
    override val primaryKey = PrimaryKey(id)
}
