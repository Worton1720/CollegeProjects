package com.example.collegeapp.features.weather.presentation.state

import com.example.collegeapp.features.weather.domain.model.WeatherForecast

/**
 * Состояние UI для экрана погоды
 */
sealed class WeatherState {

    /**
     * Начальное состояние
     */
    object Idle : WeatherState()

    /**
     * Загрузка данных
     */
    object Loading : WeatherState()

    /**
     * Данные успешно загружены
     */
    data class Success(val weatherForecast: WeatherForecast) : WeatherState()

    /**
     * Ошибка при загрузке
     */
    data class Error(val message: String) : WeatherState()
}
