package com.example.collegeapp.features.weather.domain.usecase

import com.example.collegeapp.features.weather.domain.model.WeatherForecast
import com.example.collegeapp.features.weather.domain.repository.WeatherRepository

/**
 * UseCase для получения прогноза погоды
 * Инкапсулирует бизнес-логику получения данных о погоде
 */
class GetWeatherForecastUseCase(
    private val repository: WeatherRepository
) {

    /**
     * Выполнить UseCase - получить прогноз погоды для города
     * @param city Название города
     * @return WeatherForecast с данными прогноза
     */
    suspend operator fun invoke(city: String): WeatherForecast {
        return repository.getWeatherForecast(city)
    }
}
