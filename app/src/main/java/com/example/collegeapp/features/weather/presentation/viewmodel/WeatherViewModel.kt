package com.example.collegeapp.features.weather.presentation.viewmodel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.collegeapp.features.weather.domain.usecase.GetWeatherForecastUseCase
import com.example.collegeapp.features.weather.presentation.state.WeatherState
import kotlinx.coroutines.launch

/**
 * ViewModel для экрана погоды
 * Управляет состоянием UI и вызывает UseCase'ы
 */
class WeatherViewModel(
    private val getWeatherForecastUseCase: GetWeatherForecastUseCase
) : ViewModel() {

    private val _state = MutableLiveData<WeatherState>(WeatherState.Idle)
    val state: LiveData<WeatherState> = _state

    /**
     * Получить прогноз погоды для города
     */
    fun fetchWeather(city: String) {
        viewModelScope.launch {
            _state.value = WeatherState.Loading
            try {
                val weatherForecast = getWeatherForecastUseCase(city)
                _state.value = WeatherState.Success(weatherForecast)
            } catch (e: Exception) {
                _state.value = WeatherState.Error(e.message ?: "Неизвестная ошибка")
            }
        }
    }
}
