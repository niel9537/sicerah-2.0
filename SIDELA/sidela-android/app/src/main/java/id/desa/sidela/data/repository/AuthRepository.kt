package id.desa.sidela.data.repository

import id.desa.sidela.data.local.DataStoreManager
import id.desa.sidela.data.model.ApiResponse
import id.desa.sidela.data.model.AuthResponse
import id.desa.sidela.data.model.LoginRequest
import id.desa.sidela.data.model.UserProfile
import id.desa.sidela.data.remote.ApiService
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class AuthRepository @Inject constructor(
    private val apiService: ApiService,
    private val dataStoreManager: DataStoreManager
) {
    val authToken: Flow<String?> = dataStoreManager.authToken
    val fullName: Flow<String?> = dataStoreManager.fullName
    val roleSlug: Flow<String?> = dataStoreManager.roleSlug

    suspend fun login(username: String, password: String): Result<AuthResponse> {
        return try {
            val response = apiService.login(LoginRequest(username, password))
            if (response.isSuccessful && response.body()?.data != null) {
                val authData = response.body()!!.data!!
                dataStoreManager.saveAuthSession(
                    token = authData.token,
                    uId = authData.user.id,
                    username = authData.user.username,
                    fullName = authData.user.fullName,
                    role = authData.user.role.slug,
                    avatarUrl = authData.user.avatarUrl
                )
                Result.success(authData)
            } else {
                val msg = response.body()?.message ?: "Login gagal. Periksa username dan password."
                Result.failure(Exception(msg))
            }
        } catch (e: Exception) {
            // Offline mock / fallback for initial preview
            Result.failure(e)
        }
    }

    suspend fun logout() {
        dataStoreManager.clearSession()
    }
}
