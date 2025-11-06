package com.example.collegeapp.features.weather.presentation.ui

import android.annotation.SuppressLint
import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.RecyclerView
import com.example.collegeapp.R
import com.example.collegeapp.databinding.ItemWeatherForecastBinding
import com.example.collegeapp.features.weather.domain.model.City
import com.example.collegeapp.features.weather.domain.model.ForecastItem

class WeatherAdapter(
    private var forecasts: List<ForecastItem>,
    private var city: List<City>,
    private val onItemClicked: (ForecastItem) -> Unit
) : RecyclerView.Adapter<WeatherAdapter.WeatherViewHolder>() {

    private var currentCity: City? = null

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): WeatherViewHolder {
        val binding = ItemWeatherForecastBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return WeatherViewHolder(binding)
    }

    override fun onBindViewHolder(holder: WeatherViewHolder, position: Int) {
        holder.bind(forecasts[position], currentCity)
        holder.itemView.setOnClickListener { onItemClicked(forecasts[position]) }
    }

    override fun getItemCount() = forecasts.size

    @SuppressLint("NotifyDataSetChanged")
    fun updateData(newForecasts: List<ForecastItem>, newCity: City) {
        forecasts = newForecasts
        currentCity = newCity
        notifyDataSetChanged()
    }

    class WeatherViewHolder(private val binding: ItemWeatherForecastBinding) : RecyclerView.ViewHolder(binding.root) {
        @SuppressLint("SetTextI18n")
        fun bind(forecast: ForecastItem, city: City?) {
            val context = binding.root.context
            binding.dateTextView.text = forecast.dateTimeText
            if (city != null) {
                binding.cityCountryTextView.text = "${city.name}, ${city.country}"
            } else {
                binding.cityCountryTextView.text = context.getString(R.string.city_country_placeholder)
            }
            binding.temperatureTextView.text = context.getString(R.string.temperature_format, forecast.temperature.toInt())

            val weatherDescription = when {
                forecast.weatherDescription.contains("clear", ignoreCase = true) -> "Ясно"
                forecast.weatherDescription.contains("cloud", ignoreCase = true) -> "Облачно"
                forecast.weatherDescription.contains("rain", ignoreCase = true) -> "Дождь"
                else -> forecast.weatherDescription
            }
            binding.weatherDescriptionTextView.text = weatherDescription

            // Меняем цвет карточки в зависимости от температуры
            val cardColor = when {
                forecast.temperature < 10 -> ContextCompat.getColor(context, R.color.weather_cold)
                forecast.temperature > 25 -> ContextCompat.getColor(context, R.color.weather_hot)
                else -> ContextCompat.getColor(context, R.color.weather_normal)
            }
            binding.root.setCardBackgroundColor(cardColor)

            // Устанавливаем иконку погоды
            val weatherIcon = when {
                forecast.weatherDescription.contains("clear", ignoreCase = true) -> R.drawable.ic_sun
                forecast.weatherDescription.contains("cloud", ignoreCase = true) -> R.drawable.ic_cloud
                forecast.weatherDescription.contains("rain", ignoreCase = true) -> R.drawable.ic_rain
                else -> android.R.drawable.ic_dialog_info
            }
            binding.weatherIconImageView.setImageResource(weatherIcon)
        }
    }
}
