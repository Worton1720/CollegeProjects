package com.example.collegeapp.features.weather.domain.model

import java.io.Serializable

/**
 * Domain модели для погоды
 * Используются во всех слоях приложения внутри доменной логики
 */

data class WeatherForecast(
    val forecasts: List<ForecastItem>,
    val city: City
) : Serializable

data class ForecastItem(
    val dateTime: Long,
    val temperature: Double,
    val feelsLike: Double,
    val tempMin: Double,
    val tempMax: Double,
    val pressure: Int,
    val humidity: Int,
    val weatherDescription: String,
    val weatherIcon: String,
    val windSpeed: Double,
    val windDegree: Int,
    val visibility: Int,
    val precipitationProbability: Double,
    val dateTimeText: String
) : Serializable

data class City(
    val id: Int,
    val name: String,
    val country: String,
    val sunrise: Long,
    val sunset: Long
) : Serializable
