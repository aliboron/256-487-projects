package com.ctis487.project.digitalgamemarket.adapter

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.ctis487.project.digitalgamemarket.model.Game
import com.ctis487.project.digitalgamemarket.R
import com.ctis487.project.digitalgamemarket.databinding.FeaturedRecyclerItemBinding

class FeaturedGamesAdapter(
    private var gamesMap: Map<Game, String>,
    private val onGameClick: (Game) -> Unit
) : RecyclerView.Adapter<FeaturedGamesAdapter.FeaturedGameViewHolder>() {

    inner class FeaturedGameViewHolder(val binding: FeaturedRecyclerItemBinding) : RecyclerView.ViewHolder(binding.root)
    var gameList = ArrayList<Game>(gamesMap.keys)

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): FeaturedGameViewHolder {
        val binding = FeaturedRecyclerItemBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return FeaturedGameViewHolder(binding)
    }

    override fun onBindViewHolder(holder: FeaturedGameViewHolder, position: Int) {
        val game = gameList[position]


        holder.binding.tvFeaturedGameName.text = game.name


        Glide.with(holder.itemView.context)
            .load(gamesMap.get(game))
            .placeholder(android.R.drawable.ic_menu_gallery)
            .into(holder.binding.imgFeaturedGame)

        holder.itemView.setOnClickListener {
            onGameClick(game)
        }
    }

    override fun getItemCount(): Int = gameList.size


    fun updateList(newGames: Map<Game, String>) {
        gameList = ArrayList(newGames.keys)
        gamesMap = newGames
        notifyDataSetChanged()
    }
}