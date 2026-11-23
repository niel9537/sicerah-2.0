package id.desa.sidela.utils

import android.annotation.SuppressLint
import android.content.Context
import android.location.Location
import com.google.android.gms.location.LocationServices
import com.google.android.gms.location.Priority
import com.google.android.gms.tasks.CancellationTokenSource
import kotlin.coroutines.resume
import kotlin.coroutines.suspendCancellableCoroutine
import kotlin.math.*

object LocationHelper {

    // Koordinat Resmi Kantor Pemerintah Desa Iraonogela, Kec. Mandrehe, Kab. Nias Barat
    const val OFFICE_LATITUDE = 1.0425000
    const val OFFICE_LONGITUDE = 97.5258000
    const val DEFAULT_RADIUS_METERS = 50.0

    @SuppressLint("MissingPermission")
    suspend fun getCurrentLocation(context: Context): Location? {
        val fusedClient = LocationServices.getFusedLocationProviderClient(context)
        val cts = CancellationTokenSource()

        return suspendCancellableCoroutine { continuation ->
            try {
                fusedClient.getCurrentLocation(Priority.PRIORITY_HIGH_ACCURACY, cts.token)
                    .addOnSuccessListener { loc ->
                        continuation.resume(loc)
                    }
                    .addOnFailureListener {
                        continuation.resume(null)
                    }
            } catch (e: Exception) {
                continuation.resume(null)
            }
        }
    }

    fun calculateDistanceMeters(
        lat1: Double,
        lon1: Double,
        lat2: Double = OFFICE_LATITUDE,
        lon2: Double = OFFICE_LONGITUDE
    ): Double {
        val earthRadius = 6371000.0
        val dLat = Math.toRadians(lat2 - lat1)
        val dLon = Math.toRadians(lon2 - lon1)
        val rLat1 = Math.toRadians(lat1)
        val rLat2 = Math.toRadians(lat2)

        val a = sin(dLat / 2).pow(2.0) +
                cos(rLat1) * cos(rLat2) * sin(dLon / 2).pow(2.0)
        val c = 2 * atan2(sqrt(a), sqrt(1 - a))

        return earthRadius * c
    }

    fun isWithinOfficeRadius(
        lat: Double,
        lon: Double,
        allowedRadius: Double = DEFAULT_RADIUS_METERS
    ): Pair<Boolean, Double> {
        val dist = calculateDistanceMeters(lat, lon)
        return Pair(dist <= allowedRadius, dist)
    }
}
