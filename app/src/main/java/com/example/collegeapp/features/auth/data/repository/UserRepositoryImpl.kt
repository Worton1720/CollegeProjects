package com.example.collegeapp.features.auth.data.repository

import com.example.collegeapp.features.auth.data.local.dao.FavoriteCityDao
import com.example.collegeapp.features.auth.data.local.dao.UserDao
import com.example.collegeapp.features.auth.data.local.entity.FavoriteCityEntity
import com.example.collegeapp.features.auth.data.local.entity.UserEntity
import com.example.collegeapp.features.auth.domain.repository.UserRepository

class UserRepositoryImpl(

    private val userDao: UserDao,

    private val favoriteCityDao: FavoriteCityDao

) : UserRepository {

    override suspend fun registerUser(username: String, passwordHash: String) {

        val user = UserEntity(username = username, passwordHash = passwordHash)

        userDao.insertUser(user)

    }



    override suspend fun getUserByUsername(username: String): UserEntity? {

        return userDao.getUserByUsername(username)

    }



    override suspend fun authenticateUser(username: String, passwordHash: String): Boolean {

        val user = userDao.getUserByUsername(username)

        return user != null && user.passwordHash == passwordHash

    }



    override suspend fun addFavoriteCity(userId: Int, cityName: String) {

        val favoriteCity = FavoriteCityEntity(userId = userId, cityName = cityName)

        favoriteCityDao.addFavoriteCity(favoriteCity)

    }



    override suspend fun getFavoriteCities(userId: Int): List<String> {

        return favoriteCityDao.getFavoriteCities(userId).map { it.cityName }

    }



        override suspend fun removeFavoriteCity(userId: Int, cityName: String) {



            favoriteCityDao.removeFavoriteCity(userId, cityName)



        }



    



        override suspend fun isFavoriteCity(userId: Int, cityName: String): Boolean {



            return favoriteCityDao.getFavoriteCities(userId).any { it.cityName == cityName }



        }

}
