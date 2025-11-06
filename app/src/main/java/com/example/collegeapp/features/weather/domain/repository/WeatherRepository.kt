package com.example.collegeapp.features.weather.domain.repository

import com.example.collegeapp.features.weather.domain.model.WeatherForecast

/**
 * Интерфейс репозитория погоды (Domain слой)
 * Определяет контракт для получения данных о погоде
 */
interface WeatherRepository {

    /**
     * Получить прогноз погоды для указанного города
     * @param city Название города
     * @return WeatherForecast с данными прогноза
     */
    suspend fun getWeatherForecast(city: String): WeatherForecast
}
