package com.example.collegeapp.di

import com.example.collegeapp.features.weather.data.remote.RetrofitClient
import com.example.collegeapp.features.weather.data.remote.WeatherApiService
import com.example.collegeapp.features.weather.data.repository.WeatherRepositoryImpl
import com.example.collegeapp.features.weather.domain.repository.WeatherRepository
import com.example.collegeapp.features.weather.domain.usecase.GetWeatherForecastUseCase
import com.example.collegeapp.features.weather.presentation.viewmodel.WeatherViewModel
import org.koin.androidx.viewmodel.dsl.viewModel
import org.koin.dsl.module

/**
 * Koin модуль для фичи Weather
 * Содержит все зависимости для погоды: API, Repository, UseCase, ViewModel
 */
val weatherModule = module {

    // Retrofit singleton
    single {
        RetrofitClient.provideRetrofit()
    }

    // WeatherApiService singleton
    single<WeatherApiService> {
        RetrofitClient.provideWeatherApiService(get())
    }

    // WeatherRepository
    single<WeatherRepository> {
        WeatherRepositoryImpl(get())
    }

    // GetWeatherForecastUseCase
    factory {
        GetWeatherForecastUseCase(get())
    }

    // WeatherViewModel
    viewModel {
        WeatherViewModel(get(), get())
    }
}
