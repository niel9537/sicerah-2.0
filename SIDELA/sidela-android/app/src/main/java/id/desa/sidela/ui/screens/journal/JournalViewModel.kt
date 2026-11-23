package id.desa.sidela.ui.screens.journal

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import dagger.hilt.android.lifecycle.HiltViewModel
import id.desa.sidela.data.model.WorkJournalItem
import id.desa.sidela.data.remote.ApiService
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

data class JournalUiState(
    val isLoading: Boolean = false,
    val items: List<WorkJournalItem> = emptyList(),
    val selectedFilter: String = "ALL",
    val errorMessage: String? = null
)

@HiltViewModel
class JournalViewModel @Inject constructor(
    private val apiService: ApiService
) : ViewModel() {

    private val _uiState = MutableStateFlow(JournalUiState(isLoading = true))
    val uiState: StateFlow<JournalUiState> = _uiState.asStateFlow()

    init {
        loadJournals()
    }

    fun loadJournals(status: String? = null) {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true)
            try {
                val filterParam = if (status == "ALL" || status == null) null else status
                val res = apiService.getJournals(status = filterParam)
                if (res.isSuccessful && res.body()?.data != null) {
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        items = res.body()!!.data!!,
                        selectedFilter = status ?: "ALL"
                    )
                } else {
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        items = mockJournals(),
                        selectedFilter = status ?: "ALL"
                    )
                }
            } catch (e: Exception) {
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    items = mockJournals(),
                    selectedFilter = status ?: "ALL"
                )
            }
        }
    }

    private fun mockJournals(): List<WorkJournalItem> {
        return listOf(
            WorkJournalItem(
                id = 1, userId = 6, userName = "Rachel Kim", userJabatan = "Kaur TU & Umum",
                date = "Monday, January 26, 2026",
                title = "New Year Brand Campaign Meeting",
                description = "Rapat koordinasi publikasi program pembangunan desa tahun 2026",
                startTime = "10.00", endTime = "11.00",
                workOutput = "Notulensi rapat dan timeline publikasi",
                status = "BACKLOG", feedbackNotes = null, verifiedBy = null, verifiedAt = null,
                createdAt = "2026-01-26 09:30:00"
            ),
            WorkJournalItem(
                id = 2, userId = 6, userName = "Rachel Kim", userJabatan = "Kaur TU & Umum",
                date = "Monday, January 26, 2026",
                title = "Meet New Client (Culture Dream)",
                description = "Pertemuan dengan fasilitator program kebudayaan desa",
                startTime = "09.00", endTime = "10.00",
                workOutput = "Draft MoU kegiatan",
                status = "ON_PROGRESS", feedbackNotes = null, verifiedBy = null, verifiedAt = null,
                createdAt = "2026-01-26 09:00:00"
            ),
            WorkJournalItem(
                id = 3, userId = 6, userName = "Rachel Kim", userJabatan = "Kaur TU & Umum",
                date = "Monday, January 26, 2026",
                title = "Create Moodboard for CityNeo Brand",
                description = "Penyusunan format visual papan informasi publik desa",
                startTime = "09.00", endTime = "10.00",
                workOutput = "Desain infografis APBDes 2026",
                status = "ON_PROGRESS", feedbackNotes = null, verifiedBy = null, verifiedAt = null,
                createdAt = "2026-01-26 08:30:00"
            ),
            WorkJournalItem(
                id = 4, userId = 6, userName = "Rachel Kim", userJabatan = "Kaur TU & Umum",
                date = "Monday, January 26, 2026",
                title = "Luminous Kick-Off Meeting",
                description = "Persiapan musyawarah rencana kerja pembangunan desa (RKPDes)",
                startTime = "13.00", endTime = "15.00",
                workOutput = "Dokumen TOR musdes",
                status = "ON_HOLD", feedbackNotes = null, verifiedBy = null, verifiedAt = null,
                createdAt = "2026-01-26 08:00:00"
            )
        )
    }
}
