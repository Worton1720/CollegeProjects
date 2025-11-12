package com.example.collegeapp.features.auth.data.local.database

import androidx.room.Database
import androidx.room.RoomDatabase
import com.example.collegeapp.features.auth.data.local.dao.FavoriteCityDao
import com.example.collegeapp.features.auth.data.local.dao.UserDao
import com.example.collegeapp.features.auth.data.local.entity.FavoriteCityEntity
import com.example.collegeapp.features.auth.data.local.entity.UserEntity

@Database(entities = [UserEntity::class, FavoriteCityEntity::class], version = 2, exportSchema = false)
abstract class AppDatabase : RoomDatabase() {
    abstract fun userDao(): UserDao
    abstract fun favoriteCityDao(): FavoriteCityDao
}
