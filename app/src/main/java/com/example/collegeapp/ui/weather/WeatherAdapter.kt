package com.example.collegeapp.ui.weather

import android.annotation.SuppressLint
import android.view.LayoutInflater

import android.view.ViewGroup

import androidx.core.content.ContextCompat

import androidx.recyclerview.widget.RecyclerView

import com.example.collegeapp.R

import com.example.collegeapp.data.remote.CityData
import com.example.collegeapp.data.remote.ForecastItem
import com.example.collegeapp.databinding.ItemWeatherForecastBinding

class WeatherAdapter(private var forecasts: List<ForecastItem>, private val onItemClicked: (ForecastItem) -> Unit) : RecyclerView.Adapter<WeatherAdapter.WeatherViewHolder>() {

    private var city: CityData? = null

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): WeatherViewHolder {
        val binding = ItemWeatherForecastBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return WeatherViewHolder(binding)
    }

    override fun onBindViewHolder(holder: WeatherViewHolder, position: Int) {
        holder.bind(forecasts[position], city)
        holder.itemView.setOnClickListener { onItemClicked(forecasts[position]) }
    }

    override fun getItemCount() = forecasts.size

    @SuppressLint("NotifyDataSetChanged")
    fun updateData(newForecasts: List<ForecastItem>, newCity: CityData) {
        forecasts = newForecasts
        city = newCity
        notifyDataSetChanged()
    }

    class WeatherViewHolder(private val binding: ItemWeatherForecastBinding) : RecyclerView.ViewHolder(binding.root) {
        @SuppressLint("SetTextI18n")
        fun bind(forecast: ForecastItem, city: CityData?) {
            val context = binding.root.context
            binding.dateTextView.text = forecast.dtTxt
            if (city != null) {
                binding.cityCountryTextView.text = "${city.name}, ${city.country}"
            } else {
                binding.cityCountryTextView.text = context.getString(R.string.city_country_placeholder)
            }
            binding.temperatureTextView.text = context.getString(R.string.temperature_format, forecast.main.temp.toInt())

            val weatherCondition = forecast.weather.firstOrNull()
            val weatherDescription = when (weatherCondition?.main) {
                "Clear" -> "Ясно"
                "Clouds" -> "Облачно"
                "Rain" -> "Дождь"
                else -> weatherCondition?.description ?: ""
            }
            binding.weatherDescriptionTextView.text = weatherDescription

            // Меняем цвет карточки в зависимости от температуры
            val cardColor = when {
                forecast.main.temp < 10 -> ContextCompat.getColor(context, R.color.weather_cold)
                forecast.main.temp > 25 -> ContextCompat.getColor(context, R.color.weather_hot)
                else -> ContextCompat.getColor(context, R.color.weather_normal)
            }
            binding.root.setCardBackgroundColor(cardColor)

            // Устанавливаем иконку погоды в зависимости от погодных условий
            val weatherIcon = when (weatherCondition?.main) {
                "Clear" -> R.drawable.ic_sun
                "Clouds" -> R.drawable.ic_cloud
                "Rain" -> R.drawable.ic_rain
                else -> android.R.drawable.ic_dialog_info
            }
            binding.weatherIconImageView.setImageResource(weatherIcon)
        }
    }
}
