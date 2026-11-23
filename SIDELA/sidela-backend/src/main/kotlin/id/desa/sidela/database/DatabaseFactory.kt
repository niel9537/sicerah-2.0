package id.desa.sidela.database

import com.zaxxer.hikari.HikariConfig
import com.zaxxer.hikari.HikariDataSource
import io.ktor.server.config.*
import kotlinx.coroutines.Dispatchers
import org.jetbrains.exposed.sql.Database
import org.jetbrains.exposed.sql.transactions.experimental.newSuspendedTransaction

object DatabaseFactory {
    private var dataSource: HikariDataSource? = null

    fun init(config: ApplicationConfig) {
        val dbConfig = config.config("database")
        val driver = dbConfig.property("driver").getString()
        val url = dbConfig.property("url").getString()
        val user = dbConfig.property("user").getString()
        val password = dbConfig.property("password").getString()
        val maxPoolSize = dbConfig.propertyOrNull("maxPoolSize")?.getString()?.toInt() ?: 10

        val hikariConfig = HikariConfig().apply {
            driverClassName = driver
            jdbcUrl = url
            username = user
            this.password = password
            maximumPoolSize = maxPoolSize
            isAutoCommit = false
            transactionIsolation = "TRANSACTION_REPEATABLE_READ"
            connectionTimeout = 10000
            validate()
        }

        try {
            dataSource = HikariDataSource(hikariConfig)
            Database.connect(dataSource!!)
        } catch (e: Exception) {
            System.err.println("Warning: MySQL connection failed on init (${e.message}). Ensure MySQL is running on port 3306 or configure DATABASE_URL.")
        }
    }

    suspend fun <T> dbQuery(block: suspend () -> T): T =
        newSuspendedTransaction(Dispatchers.IO) { block() }
}
