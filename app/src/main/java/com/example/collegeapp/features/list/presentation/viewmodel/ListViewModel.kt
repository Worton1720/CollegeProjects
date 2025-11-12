package com.example.collegeapp.features.list.presentation.viewmodel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.collegeapp.features.auth.domain.repository.UserRepository
import kotlinx.coroutines.launch

class ListViewModel(private val userRepository: UserRepository) : ViewModel() {

    private val _favoriteCities = MutableLiveData<List<String>>()
    val favoriteCities: LiveData<List<String>> = _favoriteCities

    fun getFavoriteCities(userId: Int) {
        viewModelScope.launch {
            _favoriteCities.value = userRepository.getFavoriteCities(userId)
        }
    }
}
