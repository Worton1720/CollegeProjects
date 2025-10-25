package com.example.collegeapp.data.remote

import com.google.gson.annotations.SerializedName
import java.io.Serializable

data class ForecastResponse(
    @SerializedName("list") val list: List<ForecastItem>,
    @SerializedName("city") val city: CityData
) : Serializable

data class ForecastItem(
    @SerializedName("dt") val dt: Long,
    @SerializedName("main") val main: MainData,
    @SerializedName("weather") val weather: List<Weather>,
    @SerializedName("wind") val wind: WindData,
    @SerializedName("visibility") val visibility: Int,
    @SerializedName("pop") val pop: Double,
    @SerializedName("dt_txt") val dtTxt: String
) : Serializable

data class MainData(
    @SerializedName("temp") val temp: Double,
    @SerializedName("feels_like") val feelsLike: Double,
    @SerializedName("temp_min") val tempMin: Double,
    @SerializedName("temp_max") val tempMax: Double,
    @SerializedName("pressure") val pressure: Int,
    @SerializedName("humidity") val humidity: Int
) : Serializable

data class Weather(
    @SerializedName("id") val id: Int,
    @SerializedName("main") val main: String,
    @SerializedName("description") val description: String,
    @SerializedName("icon") val icon: String
) : Serializable

data class WindData(
    @SerializedName("speed") val speed: Double,
    @SerializedName("deg") val deg: Int
) : Serializable

data class CityData(
    @SerializedName("id") val id: Int,
    @SerializedName("name") val name: String,
    @SerializedName("country") val country: String,
    @SerializedName("sunrise") val sunrise: Long,
    @SerializedName("sunset") val sunset: Long
) : Serializable