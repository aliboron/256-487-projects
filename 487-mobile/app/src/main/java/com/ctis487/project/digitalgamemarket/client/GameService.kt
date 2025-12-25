package com.ctis487.retrofit

import retrofit2.Call
import retrofit2.http.GET
import retrofit2.http.Query

interface RecipeService {
    @GET("posts")
    fun getRecipes(): Call<List<Recipe>>
}