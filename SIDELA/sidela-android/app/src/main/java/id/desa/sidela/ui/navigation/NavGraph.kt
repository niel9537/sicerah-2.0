package id.desa.sidela.ui.navigation

import androidx.compose.runtime.Composable
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.navigation.NavHostController
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import id.desa.sidela.ui.screens.admin.AdminSettingsScreen
import id.desa.sidela.ui.screens.attendance.AttendanceScreen
import id.desa.sidela.ui.screens.attendance.AttendanceViewModel
import id.desa.sidela.ui.screens.auth.AuthViewModel
import id.desa.sidela.ui.screens.auth.LoginScreen
import id.desa.sidela.ui.screens.dashboard.DashboardViewModel
import id.desa.sidela.ui.screens.dashboard.TodaySummaryScreen
import id.desa.sidela.ui.screens.facescan.FaceScanScreen
import id.desa.sidela.ui.screens.facescan.FaceScanViewModel
import id.desa.sidela.ui.screens.journal.JournalListScreen
import id.desa.sidela.ui.screens.journal.JournalViewModel
import id.desa.sidela.ui.screens.leave.LeaveListScreen
import id.desa.sidela.ui.screens.leave.LeaveViewModel
import id.desa.sidela.ui.screens.profile.ProfileScreen
import id.desa.sidela.ui.screens.report.ReportScreen
import id.desa.sidela.ui.screens.villagedata.VillageDataHubScreen

@Composable
fun SidelaNavGraph(
    navController: NavHostController,
    startDestination: String = Screen.Dashboard.route
) {
    NavHost(
        navController = navController,
        startDestination = startDestination
    ) {
        composable(Screen.Login.route) {
            val authViewModel = hiltViewModel<AuthViewModel>()
            LoginScreen(
                viewModel = authViewModel,
                onLoginSuccess = {
                    navController.navigate(Screen.Dashboard.route) {
                        popUpTo(Screen.Login.route) { inclusive = true }
                    }
                }
            )
        }

        // Screen 1: Dashboard / Today's Summary
        composable(Screen.Dashboard.route) {
            val dashboardViewModel = hiltViewModel<DashboardViewModel>()
            TodaySummaryScreen(
                viewModel = dashboardViewModel,
                onNavigateToAttendance = { navController.navigate(Screen.Attendance.route) },
                onNavigateToFaceScan = { navController.navigate(Screen.FaceScan.route) },
                onNavigateToRequests = { navController.navigate(Screen.Leave.route) },
                onNavigateToTasks = { navController.navigate(Screen.Journal.route) },
                onNavigateToProfile = { navController.navigate(Screen.Profile.route) }
            )
        }

        // Screen 2: Attendance
        composable(Screen.Attendance.route) {
            val attendanceViewModel = hiltViewModel<AttendanceViewModel>()
            AttendanceScreen(
                viewModel = attendanceViewModel,
                onNavigateBack = { navController.popBackStack() },
                onNavigateToFaceScan = { navController.navigate(Screen.FaceScan.route) },
                onNavigateHome = { navController.navigate(Screen.Dashboard.route) },
                onNavigateJournal = { navController.navigate(Screen.Journal.route) },
                onNavigateVillageData = { navController.navigate(Screen.VillageData.route) },
                onNavigateProfile = { navController.navigate(Screen.Profile.route) }
            )
        }

        // Screen 3: Fullscreen Camera Face Scan
        composable(Screen.FaceScan.route) {
            val faceScanViewModel = hiltViewModel<FaceScanViewModel>()
            FaceScanScreen(
                viewModel = faceScanViewModel,
                onNavigateBack = { navController.popBackStack() },
                onScanSuccess = {
                    navController.navigate(Screen.Attendance.route) {
                        popUpTo(Screen.FaceScan.route) { inclusive = true }
                    }
                }
            )
        }

        // Jurnal Kerja
        composable(Screen.Journal.route) {
            val journalViewModel = hiltViewModel<JournalViewModel>()
            JournalListScreen(
                viewModel = journalViewModel,
                onNavigateBack = { navController.popBackStack() },
                onNavigateCreate = { /* Open create journal dialog */ },
                onNavigateHome = { navController.navigate(Screen.Dashboard.route) },
                onNavigateAttendance = { navController.navigate(Screen.Attendance.route) },
                onNavigateVillageData = { navController.navigate(Screen.VillageData.route) },
                onNavigateProfile = { navController.navigate(Screen.Profile.route) }
            )
        }

        // Cuti & Pengajuan
        composable(Screen.Leave.route) {
            val leaveViewModel = hiltViewModel<LeaveViewModel>()
            LeaveListScreen(
                viewModel = leaveViewModel,
                onNavigateBack = { navController.popBackStack() },
                onNavigateCreate = { /* Open leave form */ },
                onNavigateHome = { navController.navigate(Screen.Dashboard.route) },
                onNavigateAttendance = { navController.navigate(Screen.Attendance.route) },
                onNavigateJournal = { navController.navigate(Screen.Journal.route) },
                onNavigateProfile = { navController.navigate(Screen.Profile.route) }
            )
        }

        // Data Desa Hub
        composable(Screen.VillageData.route) {
            VillageDataHubScreen(
                onNavigateBack = { navController.popBackStack() },
                onNavigateHome = { navController.navigate(Screen.Dashboard.route) },
                onNavigateAttendance = { navController.navigate(Screen.Attendance.route) },
                onNavigateJournal = { navController.navigate(Screen.Journal.route) },
                onNavigateProfile = { navController.navigate(Screen.Profile.route) }
            )
        }

        // Profil
        composable(Screen.Profile.route) {
            ProfileScreen(
                onNavigateBack = { navController.popBackStack() },
                onNavigateHome = { navController.navigate(Screen.Dashboard.route) },
                onNavigateAttendance = { navController.navigate(Screen.Attendance.route) },
                onNavigateJournal = { navController.navigate(Screen.Journal.route) },
                onNavigateVillageData = { navController.navigate(Screen.VillageData.route) },
                onNavigateAdmin = { navController.navigate(Screen.AdminSettings.route) },
                onNavigateReports = { navController.navigate(Screen.Reports.route) },
                onLogout = {
                    navController.navigate(Screen.Login.route) {
                        popUpTo(0) { inclusive = true }
                    }
                }
            )
        }

        // Pengaturan Admin
        composable(Screen.AdminSettings.route) {
            AdminSettingsScreen(
                onNavigateBack = { navController.popBackStack() }
            )
        }

        // Laporan & Ekspor
        composable(Screen.Reports.route) {
            ReportScreen(
                onNavigateBack = { navController.popBackStack() }
            )
        }
    }
}
