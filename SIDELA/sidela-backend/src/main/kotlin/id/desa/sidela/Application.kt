package id.desa.sidela

import id.desa.sidela.database.DatabaseFactory
import id.desa.sidela.plugins.*
import io.ktor.server.application.*
import io.ktor.server.engine.*
import io.ktor.server.netty.*

fun main(args: Array<String>): Unit = EngineMain.main(args)

fun Application.module() {
    DatabaseFactory.init(environment.config)
    configureSecurity()
    configureSerialization()
    configureCORS()
    configureMonitoring()
    configureStatusPages()
    configureRouting()
}
