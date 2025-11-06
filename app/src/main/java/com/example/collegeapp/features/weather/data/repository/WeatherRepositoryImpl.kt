package com.example.collegeapp.features.weather.data.repository

import com.example.collegeapp.BuildConfig
import com.example.collegeapp.features.weather.data.remote.WeatherApiService
import com.example.collegeapp.features.weather.data.remote.WeatherMapper
import com.example.collegeapp.features.weather.domain.model.WeatherForecast
import com.example.collegeapp.features.weather.domain.repository.WeatherRepository

/**
 * Реализация репозитория погоды (Data слой)
 * Получает данные из API и преобразует их в Domain модели
 */
class WeatherRepositoryImpl(
    private val apiService: WeatherApiService
) : WeatherRepository {

    override suspend fun getWeatherForecast(city: String): WeatherForecast {
        val apiKey = BuildConfig.OPENWEATHER_API_KEY
        val response = apiService.getDailyForecast(city, apiKey)
        return WeatherMapper.toDomain(response)
    }
}
