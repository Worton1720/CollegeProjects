package com.example.collegeapp.features.auth.presentation.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.collegeapp.features.auth.domain.repository.UserRepository
import kotlinx.coroutines.launch

class RegisterViewModel(private val userRepository: UserRepository) : ViewModel() {

    fun registerUser(username: String, passwordHash: String) {
        viewModelScope.launch {
            userRepository.registerUser(username, passwordHash)
        }
    }
}
