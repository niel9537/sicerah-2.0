package id.desa.sidela.utils

import kotlin.math.*

object GeoLocationUtil {
    private const val EARTH_RADIUS_METERS = 6371000.0

    /**
     * Menghitung jarak antara 2 koordinat (Latitude, Longitude) dalam meter menggunakan rumus Haversine.
     */
    fun calculateDistanceMeters(
        lat1: Double,
        lon1: Double,
        lat2: Double,
        lon2: Double
    ): Double {
        val dLat = Math.toRadians(lat2 - lat1)
        val dLon = Math.toRadians(lon2 - lon1)

        val rLat1 = Math.toRadians(lat1)
        val rLat2 = Math.toRadians(lat2)

        val a = sin(dLat / 2).pow(2.0) +
                cos(rLat1) * cos(rLat2) * sin(dLon / 2).pow(2.0)

        val c = 2 * atan2(sqrt(a), sqrt(1 - a))

        return EARTH_RADIUS_METERS * c
    }

    /**
     * Memeriksa apakah pengguna berada di dalam batas radius yang diizinkan oleh kantor desa.
     */
    fun isWithinRadius(
        userLat: Double,
        userLon: Double,
        officeLat: Double,
        officeLon: Double,
        allowedRadiusMeters: Int
    ): Pair<Boolean, Double> {
        val distance = calculateDistanceMeters(userLat, userLon, officeLat, officeLon)
        return Pair(distance <= allowedRadiusMeters, distance)
    }
}
