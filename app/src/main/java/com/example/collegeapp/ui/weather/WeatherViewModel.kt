package com.example.collegeapp.ui.weather

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.collegeapp.data.remote.ForecastResponse
import com.example.collegeapp.data.repository.WeatherRepository
import kotlinx.coroutines.launch

import com.example.collegeapp.BuildConfig

class WeatherViewModel : ViewModel() {

    private val repository = WeatherRepository()

    private val _weatherData = MutableLiveData<ForecastResponse>()
    val weatherData: LiveData<ForecastResponse> = _weatherData

    private val _error = MutableLiveData<String>()
    val error: LiveData<String> = _error

    fun fetchWeather(city: String) {
        viewModelScope.launch {
            try {
                val apiKey = BuildConfig.OPENWEATHER_API_KEY
                val response = repository.getDailyForecast(city, apiKey)
                _weatherData.postValue(response)
            } catch (e: Exception) {
                _error.postValue(e.message)
            }
        }
    }
}
