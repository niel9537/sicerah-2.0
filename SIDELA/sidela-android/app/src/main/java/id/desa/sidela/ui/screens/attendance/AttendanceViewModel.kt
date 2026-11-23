package id.desa.sidela.ui.screens.attendance

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import dagger.hilt.android.lifecycle.HiltViewModel
import id.desa.sidela.data.model.AttendanceRecord
import id.desa.sidela.data.model.TodayAttendanceSummary
import id.desa.sidela.data.repository.AttendanceRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

data class AttendanceUiState(
    val isLoading: Boolean = false,
    val todaySummary: TodayAttendanceSummary? = null,
    val historyLogs: List<AttendanceRecord> = emptyList(),
    val errorMessage: String? = null
)

@HiltViewModel
class AttendanceViewModel @Inject constructor(
    private val attendanceRepository: AttendanceRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(AttendanceUiState(isLoading = true))
    val uiState: StateFlow<AttendanceUiState> = _uiState.asStateFlow()

    init {
        loadData()
    }

    fun loadData() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true)

            val summaryRes = attendanceRepository.getTodayAttendance()
            val historyRes = attendanceRepository.getAttendanceHistory()

            _uiState.value = AttendanceUiState(
                isLoading = false,
                todaySummary = summaryRes.getOrNull(),
                historyLogs = historyRes.getOrDefault(emptyList()),
                errorMessage = null
            )
        }
    }
}
