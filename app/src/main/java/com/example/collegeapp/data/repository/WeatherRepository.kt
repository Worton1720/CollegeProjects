package com.example.collegeapp.data.repository

import com.example.collegeapp.data.remote.ForecastResponse
import com.example.collegeapp.data.remote.RetrofitClient

class WeatherRepository {

    private val weatherApiService = RetrofitClient.instance

    suspend fun getDailyForecast(city: String, apiKey: String): ForecastResponse {
        return weatherApiService.getDailyForecast(city, apiKey = apiKey)
    }
}
