package id.desa.sidela.models

import kotlinx.serialization.Serializable

@Serializable
data class ReportFilterRequest(
    val startDate: String,
    val endDate: String,
    val userId: Long? = null,
    val status: String? = null
)

@Serializable
data class AttendanceReportDTO(
    val villageHeader: VillageProfileDTO,
    val period: String,
    val generatedAt: String,
    val generatedBy: String,
    val summary: AttendanceSummaryCounters,
    val records: List<AttendanceResponse>
)

@Serializable
data class ExportUrlResponse(
    val downloadUrl: String,
    val fileName: String,
    val fileType: String
)
