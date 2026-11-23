package id.desa.sidela.services

import id.desa.sidela.database.*
import id.desa.sidela.models.*
import org.jetbrains.exposed.sql.*
import java.time.LocalDate
import java.time.LocalDateTime
import java.time.LocalTime
import java.time.format.DateTimeFormatter

object JournalService {

    private val timeFormatter = DateTimeFormatter.ofPattern("HH:mm")

    suspend fun createJournal(userId: Long, input: JournalInput): JournalResponse {
        val journalDate = LocalDate.parse(input.date)
        val sTime = LocalTime.parse(input.startTime)
        val eTime = LocalTime.parse(input.endTime)

        return DatabaseFactory.dbQuery {
            val insertedId = WorkJournalsTable.insert {
                it[WorkJournalsTable.userId] = userId
                it[date] = journalDate
                it[title] = input.title
                it[description] = input.description
                it[startTime] = sTime
                it[endTime] = eTime
                it[workOutput] = input.workOutput
                it[status] = input.status
                it[createdAt] = LocalDateTime.now()
            } get WorkJournalsTable.id

            input.attachments.forEach { att ->
                JournalAttachmentsTable.insert {
                    it[journalId] = insertedId
                    it[fileName] = att.fileName
                    it[fileUrl] = att.fileUrl
                    it[fileType] = att.fileType
                    it[fileSizeBytes] = att.fileSizeBytes
                }
            }

            buildJournalResponse(insertedId)
        }
    }

    suspend fun getJournals(userId: Long?, roleSlug: String?, date: String? = null, status: String? = null): List<JournalResponse> {
        return DatabaseFactory.dbQuery {
            var query = WorkJournalsTable.selectAll()

            if (roleSlug == "perangkat_desa" && userId != null) {
                query = query.andWhere { WorkJournalsTable.userId eq userId }
            }

            if (!date.isNullOrBlank()) {
                query = query.andWhere { WorkJournalsTable.date eq LocalDate.parse(date) }
            }

            if (!status.isNullOrBlank()) {
                query = query.andWhere { WorkJournalsTable.status eq status.uppercase() }
            }

            query.orderBy(WorkJournalsTable.createdAt to SortOrder.DESC)
                .limit(50)
                .map { mapJournalRow(it) }
        }
    }

    suspend fun updateJournal(journalId: Long, userId: Long, input: JournalInput): JournalResponse {
        return DatabaseFactory.dbQuery {
            val record = WorkJournalsTable.select { WorkJournalsTable.id eq journalId }.singleOrNull()
                ?: throw IllegalArgumentException("Jurnal tidak ditemukan.")

            if (record[WorkJournalsTable.userId] != userId) {
                throw IllegalStateException("Anda tidak memiliki izin mengedit jurnal ini.")
            }

            if (record[WorkJournalsTable.status] == "COMPLETED" && record[WorkJournalsTable.verifiedBy] != null) {
                throw IllegalStateException("Jurnal yang telah diverifikasi oleh atasan tidak dapat diubah.")
            }

            WorkJournalsTable.update({ WorkJournalsTable.id eq journalId }) {
                it[title] = input.title
                it[description] = input.description
                it[startTime] = LocalTime.parse(input.startTime)
                it[endTime] = LocalTime.parse(input.endTime)
                it[workOutput] = input.workOutput
                it[status] = input.status
            }

            buildJournalResponse(journalId)
        }
    }

    suspend fun verifyJournal(journalId: Long, reviewerId: Long, input: JournalVerifyInput): JournalResponse {
        return DatabaseFactory.dbQuery {
            WorkJournalsTable.update({ WorkJournalsTable.id eq journalId }) {
                it[status] = input.status
                it[feedbackNotes] = input.feedbackNotes
                it[verifiedBy] = reviewerId
                it[verifiedAt] = LocalDateTime.now()
            }

            buildJournalResponse(journalId)
        }
    }

    private fun buildJournalResponse(id: Long): JournalResponse {
        val row = WorkJournalsTable.select { WorkJournalsTable.id eq id }.single()
        return mapJournalRow(row)
    }

    private fun mapJournalRow(row: ResultRow): JournalResponse {
        val jId = row[WorkJournalsTable.id]
        val uId = row[WorkJournalsTable.userId]
        val userRow = UsersTable.select { UsersTable.id eq uId }.singleOrNull()
        val perangkatRow = PerangkatDesaTable.select { PerangkatDesaTable.userId eq uId }.singleOrNull()

        val verifierName = row[WorkJournalsTable.verifiedBy]?.let { vId ->
            UsersTable.select { UsersTable.id eq vId }.singleOrNull()?.get(UsersTable.fullName)
        }

        val attachments = JournalAttachmentsTable.select { JournalAttachmentsTable.journalId eq jId }.map {
            JournalAttachmentDTO(
                id = it[JournalAttachmentsTable.id],
                journalId = jId,
                fileName = it[JournalAttachmentsTable.fileName],
                fileUrl = it[JournalAttachmentsTable.fileUrl],
                fileType = it[JournalAttachmentsTable.fileType],
                fileSizeBytes = it[JournalAttachmentsTable.fileSizeBytes]
            )
        }

        return JournalResponse(
            id = jId,
            userId = uId,
            userName = userRow?.get(UsersTable.fullName) ?: "Aparatur Desa",
            userJabatan = perangkatRow?.get(PerangkatDesaTable.jabatan) ?: "Perangkat Desa",
            date = row[WorkJournalsTable.date].toString(),
            title = row[WorkJournalsTable.title],
            description = row[WorkJournalsTable.description],
            startTime = row[WorkJournalsTable.startTime].format(timeFormatter),
            endTime = row[WorkJournalsTable.endTime].format(timeFormatter),
            workOutput = row[WorkJournalsTable.workOutput],
            status = row[WorkJournalsTable.status],
            feedbackNotes = row[WorkJournalsTable.feedbackNotes],
            verifiedBy = verifierName,
            verifiedAt = row[WorkJournalsTable.verifiedAt]?.toString(),
            attachments = attachments,
            createdAt = row[WorkJournalsTable.createdAt].toString()
        )
    }
}
