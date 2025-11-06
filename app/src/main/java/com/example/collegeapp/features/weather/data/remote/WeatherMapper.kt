package com.example.collegeapp.features.weather.data.remote

import com.example.collegeapp.features.weather.data.remote.dto.CityDto
import com.example.collegeapp.features.weather.data.remote.dto.ForecastItemDto
import com.example.collegeapp.features.weather.data.remote.dto.ForecastResponse
import com.example.collegeapp.features.weather.domain.model.City
import com.example.collegeapp.features.weather.domain.model.ForecastItem
import com.example.collegeapp.features.weather.domain.model.WeatherForecast

/**
 * Маппер для преобразования DTO из API в Domain модели
 */
object WeatherMapper {

    /**
     * Преобразовать ForecastResponse (DTO) в WeatherForecast (Domain)
     */
    fun toDomain(response: ForecastResponse): WeatherForecast {
        return WeatherForecast(
            forecasts = response.list.map { toDomain(it) },
            city = toDomain(response.city)
        )
    }

    /**
     * Преобразовать ForecastItemDto в ForecastItem (Domain)
     */
    private fun toDomain(dto: ForecastItemDto): ForecastItem {
        return ForecastItem(
            dateTime = dto.dt,
            temperature = dto.main.temp,
            feelsLike = dto.main.feelsLike,
            tempMin = dto.main.tempMin,
            tempMax = dto.main.tempMax,
            pressure = dto.main.pressure,
            humidity = dto.main.humidity,
            weatherDescription = dto.weather.firstOrNull()?.description ?: "",
            weatherIcon = dto.weather.firstOrNull()?.icon ?: "",
            windSpeed = dto.wind.speed,
            windDegree = dto.wind.deg,
            visibility = dto.visibility,
            precipitationProbability = dto.pop,
            dateTimeText = dto.dtTxt
        )
    }

    /**
     * Преобразовать CityDto в City (Domain)
     */
    private fun toDomain(dto: CityDto): City {
        return City(
            id = dto.id,
            name = dto.name,
            country = dto.country,
            sunrise = dto.sunrise,
            sunset = dto.sunset
        )
    }
}
