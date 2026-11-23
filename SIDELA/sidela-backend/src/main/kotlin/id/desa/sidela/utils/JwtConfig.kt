package id.desa.sidela.utils

import com.auth0.jwt.JWT
import com.auth0.jwt.JWTVerifier
import com.auth0.jwt.algorithms.Algorithm
import java.util.*

object JwtConfig {
    private const val SECRET = "sidela_desa_iraonogela_super_secure_jwt_secret_key_2026_xyz!"
    private const val ISSUER = "https://iraonogela.desa.id"
    private const val AUDIENCE = "sidela-mobile-app"
    private val algorithm = Algorithm.HMAC256(SECRET)

    val verifier: JWTVerifier = JWT.require(algorithm)
        .withAudience(AUDIENCE)
        .withIssuer(ISSUER)
        .build()

    fun generateToken(userId: Long, username: String, roleSlug: String): String {
        val validityMs = 30L * 24 * 60 * 60 * 1000 // 30 days
        val expiresAt = Date(System.currentTimeMillis() + validityMs)

        return JWT.create()
            .withAudience(AUDIENCE)
            .withIssuer(ISSUER)
            .withClaim("userId", userId)
            .withClaim("username", username)
            .withClaim("role", roleSlug)
            .withExpiresAt(expiresAt)
            .sign(algorithm)
    }

    fun getExpirationSeconds(): Long {
        return 30L * 24 * 60 * 60
    }
}
