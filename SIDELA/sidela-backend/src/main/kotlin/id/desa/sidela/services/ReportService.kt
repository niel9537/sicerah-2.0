package id.desa.sidela.services

import id.desa.sidela.models.*
import java.io.File
import java.time.LocalDate
import java.time.format.DateTimeFormatter
import java.util.UUID

object ReportService {

    private val reportDir = File("./uploads/reports").apply {
        if (!exists()) mkdirs()
    }

    suspend fun generateAttendanceReport(filter: ReportFilterRequest): AttendanceReportDTO {
        val village = VillageService.getVillageProfile()
        val history = AttendanceService.getHistory(filter.userId ?: 1)
        val summary = AttendanceService.getAttendanceCountersToday()

        return AttendanceReportDTO(
            villageHeader = village,
            period = "${filter.startDate} s/d ${filter.endDate}",
            generatedAt = LocalDate.now().toString(),
            generatedBy = "Administrator SIDELA",
            summary = summary,
            records = history
        )
    }

    suspend fun exportAttendanceCsv(filter: ReportFilterRequest): ExportUrlResponse {
        val reportData = generateAttendanceReport(filter)
        val fileName = "Laporan_Presensi_Desa_Iraonogela_${UUID.randomUUID().toString().take(8)}.csv"
        val file = File(reportDir, fileName)

        val sb = StringBuilder()
        sb.append("PEMERINTAH KABUPATEN NIAS BARAT\n")
        sb.append("KECAMATAN MANDREHE - DESA IRAONOGELA\n")
        sb.append("REKAPITULASI PRESENSI APARATUR DESA\n")
        sb.append("Periode: ${reportData.period}\n\n")
        sb.append("No,Nama Aparatur,Jabatan,Tanggal,Jam Masuk,Jam Pulang,Status,Keterlambatan (menit),Total Jam Kerja,Metode\n")

        reportData.records.forEachIndexed { idx, item ->
            sb.append("${idx + 1},\"${item.userName}\",\"${item.userJabatan}\",${item.date},${item.clockIn ?: "-"},${item.clockOut ?: "-"},${item.status},${item.lateMinutes},${item.workHours},${item.verificationMethod}\n")
        }

        file.writeText(sb.toString())

        return ExportUrlResponse(
            downloadUrl = "/uploads/reports/$fileName",
            fileName = fileName,
            fileType = "CSV"
        )
    }

    suspend fun exportAttendancePdf(filter: ReportFilterRequest): ExportUrlResponse {
        // Generates print-ready HTML/PDF document template with official Kop Surat
        val reportData = generateAttendanceReport(filter)
        val fileName = "Laporan_Presensi_Desa_Iraonogela_${UUID.randomUUID().toString().take(8)}.html"
        val file = File(reportDir, fileName)

        val htmlContent = """
            <!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="UTF-8">
                <title>Laporan Presensi Aparatur Desa Iraonogela</title>
                <style>
                    body { font-family: 'Times New Roman', Times, serif; margin: 30px; color: #111; }
                    .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
                    .header h2 { margin: 2px 0; font-size: 18px; text-transform: uppercase; }
                    .header h3 { margin: 2px 0; font-size: 16px; text-transform: uppercase; }
                    .header p { margin: 2px 0; font-size: 12px; font-style: italic; }
                    .title { text-align: center; font-size: 15px; font-weight: bold; margin-bottom: 15px; text-decoration: underline; }
                    table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 12px; }
                    th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
                    th { background-color: #f2f2f2; text-align: center; }
                    .badge-hadir { color: #00875A; font-weight: bold; }
                    .badge-terlambat { color: #D97706; font-weight: bold; }
                    .footer { margin-top: 40px; float: right; width: 250px; text-align: center; font-size: 13px; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>PEMERINTAH KABUPATEN NIAS BARAT</h2>
                    <h3>KECAMATAN MANDREHE</h3>
                    <h2>PEMERINTAH DESA IRAONOGELA</h2>
                    <p>Alamat: Jalan Poros Desa Iraonogela, Kode Pos 22862, Email: pemdes@iraonogela.desa.id</p>
                </div>
                
                <div class="title">LAPORAN KEHADIRAN &amp; PRESENSI APARATUR DESA</div>
                <div><strong>Periode:</strong> ${reportData.period}</div>
                <div><strong>Dicetak Pada:</strong> ${reportData.generatedAt}</div>
                
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Aparatur</th>
                            <th>Jabatan</th>
                            <th>Tanggal</th>
                            <th>Masuk</th>
                            <th>Pulang</th>
                            <th>Status</th>
                            <th>Keterlambatan</th>
                            <th>Total Kerja</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${reportData.records.mapIndexed { idx, r ->
                            """
                            <tr>
                                <td style="text-align: center;">${idx + 1}</td>
                                <td>${r.userName}</td>
                                <td>${r.userJabatan}</td>
                                <td style="text-align: center;">${r.date}</td>
                                <td style="text-align: center;">${r.clockIn ?: "-"}</td>
                                <td style="text-align: center;">${r.clockOut ?: "-"}</td>
                                <td style="text-align: center;">${r.status}</td>
                                <td style="text-align: center;">${if (r.lateMinutes > 0) "${r.lateMinutes} mnt" else "-"}</td>
                                <td style="text-align: center;">${r.workHours}</td>
                            </tr>
                            """.trimIndent()
                        }.joinToString("\n")}
                    </tbody>
                </table>
                
                <div class="footer">
                    <p>Iraonogela, ${LocalDate.now().format(DateTimeFormatter.ofPattern("dd MMMM yyyy"))}</p>
                    <p>Kepala Desa Iraonogela</p>
                    <br><br><br>
                    <p><strong><u>BAZIDUHU GULO</u></strong></p>
                </div>
            </body>
            </html>
        """.trimIndent()

        file.writeText(htmlContent)

        return ExportUrlResponse(
            downloadUrl = "/uploads/reports/$fileName",
            fileName = fileName,
            fileType = "PDF_HTML"
        )
    }
}
