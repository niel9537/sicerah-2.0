package id.desa.sidela.data.local

import android.content.Context
import androidx.datastore.core.DataStore
import androidx.datastore.preferences.core.*
import androidx.datastore.preferences.preferencesDataStore
import dagger.hilt.android.qualifiers.ApplicationContext
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.map
import javax.inject.Inject
import javax.inject.Singleton

private val Context.dataStore: DataStore<Preferences> by preferencesDataStore(name = "sidela_prefs")

@Singleton
class DataStoreManager @Inject constructor(
    @ApplicationContext private val context: Context
) {
    companion object {
        val KEY_TOKEN = stringPreferencesKey("jwt_token")
        val KEY_USER_ID = longPreferencesKey("user_id")
        val KEY_USERNAME = stringPreferencesKey("username")
        val KEY_FULL_NAME = stringPreferencesKey("full_name")
        val KEY_ROLE_SLUG = stringPreferencesKey("role_slug")
        val KEY_AVATAR_URL = stringPreferencesKey("avatar_url")
        val KEY_BASE_URL = stringPreferencesKey("custom_base_url")
    }

    val authToken: Flow<String?> = context.dataStore.data.map { prefs ->
        prefs[KEY_TOKEN]
    }

    val userId: Flow<Long?> = context.dataStore.data.map { prefs ->
        prefs[KEY_USER_ID]
    }

    val fullName: Flow<String?> = context.dataStore.data.map { prefs ->
        prefs[KEY_FULL_NAME] ?: "Rachel Kim"
    }

    val roleSlug: Flow<String?> = context.dataStore.data.map { prefs ->
        prefs[KEY_ROLE_SLUG] ?: "perangkat_desa"
    }

    suspend fun saveAuthSession(token: String, uId: Long, username: String, fullName: String, role: String, avatarUrl: String?) {
        context.dataStore.edit { prefs ->
            prefs[KEY_TOKEN] = token
            prefs[KEY_USER_ID] = uId
            prefs[KEY_USERNAME] = username
            prefs[KEY_FULL_NAME] = fullName
            prefs[KEY_ROLE_SLUG] = role
            avatarUrl?.let { prefs[KEY_AVATAR_URL] = it }
        }
    }

    suspend fun clearSession() {
        context.dataStore.edit { prefs ->
            prefs.remove(KEY_TOKEN)
            prefs.remove(KEY_USER_ID)
            prefs.remove(KEY_USERNAME)
            prefs.remove(KEY_FULL_NAME)
            prefs.remove(KEY_ROLE_SLUG)
            prefs.remove(KEY_AVATAR_URL)
        }
    }
}
