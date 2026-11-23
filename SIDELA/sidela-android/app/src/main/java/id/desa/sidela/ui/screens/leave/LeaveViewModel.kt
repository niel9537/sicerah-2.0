package id.desa.sidela.ui.screens.leave

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import dagger.hilt.android.lifecycle.HiltViewModel
import id.desa.sidela.data.model.LeaveItem
import id.desa.sidela.data.model.RequestStatusCounters
import id.desa.sidela.data.remote.ApiService
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

data class LeaveUiState(
    val isLoading: Boolean = false,
    val items: List<LeaveItem> = emptyList(),
    val counters: RequestStatusCounters = RequestStatusCounters(25, 8, 16, 1),
    val selectedTab: String = "ALL"
)

@HiltViewModel
class LeaveViewModel @Inject constructor(
    private val apiService: ApiService
) : ViewModel() {

    private val _uiState = MutableStateFlow(LeaveUiState(isLoading = true))
    val uiState: StateFlow<LeaveUiState> = _uiState.asStateFlow()

    init {
        loadData()
    }

    fun loadData(tab: String = "ALL") {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true)
            try {
                val filter = if (tab == "ALL") null else tab
                val res = apiService.getLeaves(filter)
                val countersRes = apiService.getLeaveCounters()

                _uiState.value = LeaveUiState(
                    isLoading = false,
                    items = res.body()?.data ?: mockLeaves(),
                    counters = countersRes.body()?.data ?: RequestStatusCounters(25, 8, 16, 1),
                    selectedTab = tab
                )
            } catch (e: Exception) {
                _uiState.value = LeaveUiState(
                    isLoading = false,
                    items = mockLeaves(),
                    counters = RequestStatusCounters(25, 8, 16, 1),
                    selectedTab = tab
                )
            }
        }
    }

    private fun mockLeaves(): List<LeaveItem> {
        return listOf(
            LeaveItem(
                id = 1, userId = 6, userName = "Rachel Kim", userJabatan = "Kaur TU & Umum",
                leaveType = "CUTI_TAHUNAN", startDate = "2026-01-10", endDate = "2026-01-12",
                totalDays = 3, reason = "Keperluan keluarga di Gunungsitoli", attachmentUrl = null,
                destinationLocation = null, officialDutyNumber = null, status = "APPROVED",
                approvedBy = "Baziduhu Gulo (Kepala Desa)", approvedAt = "2026-01-09",
                rejectionNotes = null, createdAt = "2026-01-08"
            ),
            LeaveItem(
                id = 2, userId = 6, userName = "Rachel Kim", userJabatan = "Kaur TU & Umum",
                leaveType = "SAKIT", startDate = "2026-01-15", endDate = "2026-01-15",
                totalDays = 1, reason = "Demam dan flu (Surat Dokter)", attachmentUrl = null,
                destinationLocation = null, officialDutyNumber = null, status = "APPROVED",
                approvedBy = "Baziduhu Gulo (Kepala Desa)", approvedAt = "2026-01-15",
                rejectionNotes = null, createdAt = "2026-01-15"
            ),
            LeaveItem(
                id = 3, userId = 6, userName = "Rachel Kim", userJabatan = "Kaur TU & Umum",
                leaveType = "CUTI_TAHUNAN", startDate = "2026-02-02", endDate = "2026-02-05",
                totalDays = 4, reason = "Cuti tahunan tahap I", attachmentUrl = null,
                destinationLocation = null, officialDutyNumber = null, status = "REJECTED",
                approvedBy = "Baziduhu Gulo (Kepala Desa)", approvedAt = "2026-02-01",
                rejectionNotes = "Agenda desa padat pada awal bulan", createdAt = "2026-01-28"
            )
        )
    }
}
