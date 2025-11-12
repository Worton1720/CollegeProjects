package com.example.collegeapp.features.weather.presentation.viewmodel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.collegeapp.features.auth.domain.repository.UserRepository
import com.example.collegeapp.features.weather.domain.usecase.GetWeatherForecastUseCase
import com.example.collegeapp.features.weather.presentation.state.WeatherState
import kotlinx.coroutines.launch

/**
 * ViewModel для экрана погоды
 * Управляет состоянием UI и вызывает UseCase'ы
 */
class WeatherViewModel(
    private val getWeatherForecastUseCase: GetWeatherForecastUseCase,
    private val userRepository: UserRepository
) : ViewModel() {

    private val _state = MutableLiveData<WeatherState>(WeatherState.Idle)
    val state: LiveData<WeatherState> = _state

    private val _isFavorite = MutableLiveData<Boolean>()
    val isFavorite: LiveData<Boolean> = _isFavorite

    /**
     * Получить прогноз погоды для города
     */
    fun fetchWeather(city: String) {
        viewModelScope.launch {
            _state.value = WeatherState.Loading
            try {
                val weatherForecast = getWeatherForecastUseCase(city)
                _state.value = WeatherState.Success(weatherForecast)
                // After fetching weather, check if it's a favorite
                checkFavoriteStatus(1, city) // TODO: Get real user ID
            } catch (e: Exception) {
                _state.value = WeatherState.Error(e.message ?: "Неизвестная ошибка")
            }
        }
    }

    fun toggleFavoriteStatus(userId: Int, cityName: String) {
        viewModelScope.launch {
            if (userRepository.isFavoriteCity(userId, cityName)) {
                userRepository.removeFavoriteCity(userId, cityName)
                _isFavorite.value = false
            } else {
                userRepository.addFavoriteCity(userId, cityName)
                _isFavorite.value = true
            }
        }
    }

    private fun checkFavoriteStatus(userId: Int, cityName: String) {
        viewModelScope.launch {
            _isFavorite.value = userRepository.isFavoriteCity(userId, cityName)
        }
    }

}
