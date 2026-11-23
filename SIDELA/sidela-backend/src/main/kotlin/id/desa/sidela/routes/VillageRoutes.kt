package id.desa.sidela.routes

import id.desa.sidela.models.*
import id.desa.sidela.services.VillageService
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.auth.*
import io.ktor.server.auth.jwt.*
import io.ktor.server.request.*
import io.ktor.server.response.*
import io.ktor.server.routing.*

fun Route.villageRoutes() {
    route("/api/v1/village") {
        get("/profile") {
            val profile = VillageService.getVillageProfile()
            call.respond(
                HttpStatusCode.OK,
                ApiResponse(success = true, message = "Profil Desa Iraonogela.", data = profile)
            )
        }

        get("/hamlets") {
            val hamlets = VillageService.getHamlets()
            call.respond(
                HttpStatusCode.OK,
                ApiResponse(success = true, message = "Daftar Dusun Desa Iraonogela.", data = hamlets)
            )
        }

        authenticate("auth-jwt") {
            get("/residents") {
                val search = call.request.queryParameters["search"]
                val hamletId = call.request.queryParameters["hamletId"]?.toIntOrNull()
                val page = call.request.queryParameters["page"]?.toIntOrNull() ?: 1
                val size = call.request.queryParameters["size"]?.toIntOrNull() ?: 20

                val data = VillageService.getResidents(search, hamletId, page, size)
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Data penduduk berhasil diambil.", data = data)
                )
            }

            post("/residents") {
                val input = call.receive<ResidentInput>()
                val res = VillageService.createResident(input)
                call.respond(
                    HttpStatusCode.Created,
                    ApiResponse(success = true, message = "Data penduduk berhasil ditambahkan.", data = res)
                )
            }

            get("/assets") {
                val assets = VillageService.getAssets()
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Daftar aset desa berhasil diambil.", data = assets)
                )
            }

            get("/letters") {
                val type = call.request.queryParameters["type"]
                val letters = VillageService.getLetters(type)
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Arsip surat desa berhasil diambil.", data = letters)
                )
            }
        }
    }
}
