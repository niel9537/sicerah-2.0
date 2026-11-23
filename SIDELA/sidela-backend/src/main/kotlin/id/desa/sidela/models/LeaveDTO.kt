package id.desa.sidela.models

import kotlinx.serialization.Serializable

@Serializable
data class LeaveRequestInput(
    val leaveType: String, // CUTI_TAHUNAN, CUTI_MELAHIRKAN, IZIN, SAKIT, DINAS_LUAR
    val startDate: String,
    val endDate: String,
    val reason: String,
    val attachmentUrl: String? = null,
    val destinationLocation: String? = null,
    val officialDutyNumber: String? = null
)

@Serializable
data class LeaveResponse(
    val id: Long,
    val userId: Long,
    val userName: String,
    val userJabatan: String,
    val leaveType: String,
    val startDate: String,
    val endDate: String,
    val totalDays: Int,
    val reason: String,
    val attachmentUrl: String? = null,
    val destinationLocation: String? = null,
    val officialDutyNumber: String? = null,
    val status: String, // PENDING, APPROVED, REJECTED
    val approvedBy: String? = null,
    val approvedAt: String? = null,
    val rejectionNotes: String? = null,
    val createdAt: String
)

@Serializable
data class ApprovalActionInput(
    val action: String, // APPROVE or REJECT
    val notes: String? = null
)

@Serializable
data class RequestStatusCounters(
    val totalRequests: Int = 25,
    val approved: Int = 8,
    val rejected: Int = 16,
    val pending: Int = 1
)
