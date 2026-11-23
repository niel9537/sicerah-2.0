package id.desa.sidela.ui.navigation

sealed class Screen(val route: String) {
    object Login : Screen("login")
    object Dashboard : Screen("dashboard")
    object Attendance : Screen("attendance")
    object FaceScan : Screen("facescan")
    object Journal : Screen("journal")
    object CreateJournal : Screen("create_journal")
    object Leave : Screen("leave")
    object CreateLeave : Screen("create_leave")
    object VillageData : Screen("villagedata")
    object Profile : Screen("profile")
    object AdminSettings : Screen("admin_settings")
    object Reports : Screen("reports")
}
