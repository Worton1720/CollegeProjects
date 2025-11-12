package com.example.collegeapp.features.auth.domain.repository

import com.example.collegeapp.features.auth.data.local.entity.UserEntity

interface UserRepository {
    suspend fun registerUser(username: String, passwordHash: String)
    suspend fun getUserByUsername(username: String): UserEntity?
    suspend fun authenticateUser(username: String, passwordHash: String): Boolean
    suspend fun addFavoriteCity(userId: Int, cityName: String)
    suspend fun getFavoriteCities(userId: Int): List<String>
    suspend fun removeFavoriteCity(userId: Int, cityName: String)
    suspend fun isFavoriteCity(userId: Int, cityName: String): Boolean
}
