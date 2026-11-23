package id.desa.sidela.models

import kotlinx.serialization.Serializable

@Serializable
data class JournalInput(
    val date: String,
    val title: String,
    val description: String,
    val startTime: String,
    val endTime: String,
    val workOutput: String,
    val status: String = "ON_PROGRESS", // BACKLOG, ON_PROGRESS, COMPLETED, ON_HOLD
    val attachments: List<JournalAttachmentInput> = emptyList()
)

@Serializable
data class JournalAttachmentInput(
    val fileName: String,
    val fileUrl: String,
    val fileType: String,
    val fileSizeBytes: Long = 0
)

@Serializable
data class JournalResponse(
    val id: Long,
    val userId: Long,
    val userName: String,
    val userJabatan: String,
    val date: String,
    val title: String,
    val description: String,
    val startTime: String,
    val endTime: String,
    val workOutput: String,
    val status: String,
    val feedbackNotes: String? = null,
    val verifiedBy: String? = null,
    val verifiedAt: String? = null,
    val attachments: List<JournalAttachmentDTO> = emptyList(),
    val createdAt: String
)

@Serializable
data class JournalAttachmentDTO(
    val id: Long,
    val journalId: Long,
    val fileName: String,
    val fileUrl: String,
    val fileType: String,
    val fileSizeBytes: Long
)

@Serializable
data class JournalVerifyInput(
    val status: String = "COMPLETED",
    val feedbackNotes: String? = null
)
