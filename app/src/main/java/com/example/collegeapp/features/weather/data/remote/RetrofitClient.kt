package com.example.collegeapp.features.weather.data.remote

import com.example.collegeapp.BuildConfig
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory

/**
 * Retrofit клиент для API погоды
 * Note: В будущем будет заменён на DI через Koin
 */
object RetrofitClient {

    private const val BASE_URL = BuildConfig.OPENWEATHER_BASE_URL

    fun provideRetrofit(): Retrofit {
        return Retrofit.Builder()
            .baseUrl(BASE_URL)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
    }

    fun provideWeatherApiService(retrofit: Retrofit): WeatherApiService {
        return retrofit.create(WeatherApiService::class.java)
    }
}
