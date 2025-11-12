package com.example.collegeapp.features.list.presentation.ui

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.example.collegeapp.databinding.ItemFavoriteCityBinding

class FavoriteCityAdapter(
    private val onCityClick: (String) -> Unit
) : ListAdapter<String, FavoriteCityAdapter.ViewHolder>(FavoriteCityDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemFavoriteCityBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(private val binding: ItemFavoriteCityBinding) : RecyclerView.ViewHolder(binding.root) {
        fun bind(city: String) {
            binding.cityNameTextView.text = city
            binding.root.setOnClickListener { onCityClick(city) }
        }
    }

    class FavoriteCityDiffCallback : DiffUtil.ItemCallback<String>() {
        override fun areItemsTheSame(oldItem: String, newItem: String): Boolean {
            return oldItem == newItem
        }

        override fun areContentsTheSame(oldItem: String, newItem: String): Boolean {
            return oldItem == newItem
        }
    }
}
