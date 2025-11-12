package com.example.collegeapp.features.auth.data.local.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.example.collegeapp.features.auth.data.local.entity.FavoriteCityEntity

@Dao
interface FavoriteCityDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun addFavoriteCity(favoriteCity: FavoriteCityEntity)

    @Query("SELECT * FROM favorite_cities WHERE userId = :userId")
    suspend fun getFavoriteCities(userId: Int): List<FavoriteCityEntity>

    @Query("DELETE FROM favorite_cities WHERE userId = :userId AND cityName = :cityName")
    suspend fun removeFavoriteCity(userId: Int, cityName: String): Int
}
