package com.example.collegeapp.features.weather.data.remote

import com.example.collegeapp.features.weather.data.remote.dto.ForecastResponse
import retrofit2.http.GET
import retrofit2.http.Query

/**
 * API сервис для получения данных о погоде с OpenWeatherMap
 */
interface WeatherApiService {

    @GET("data/2.5/forecast")
    suspend fun getDailyForecast(
        @Query("q") city: String,
        @Query("appid") apiKey: String,
        @Query("units") units: String = "metric"
    ): ForecastResponse
}
