package com.ctis487.project.digitalgamemarket.adapter

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.bumptech.glide.util.Util
import com.ctis487.project.digitalgamemarket.databinding.StoreGameItemBinding
import com.ctis487.project.digitalgamemarket.db.Utils
import com.ctis487.project.digitalgamemarket.model.Game
import java.util.Locale

class StoreGamesAdapter(
    private var gameList: List<Game>,
    private val onBuyClick: (Game) -> Unit
) : RecyclerView.Adapter<StoreGamesAdapter.StoreGameViewHolder>() {

    inner class StoreGameViewHolder(val binding: StoreGameItemBinding) : RecyclerView.ViewHolder(binding.root)

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): StoreGameViewHolder {
        val binding = StoreGameItemBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return StoreGameViewHolder(binding)
    }

    override fun onBindViewHolder(holder: StoreGameViewHolder, position: Int) {
        val game = gameList[position]

        holder.binding.tvGameName.text = game.name
        holder.binding.tvGameGenre.text = game.genre

        val bannerURL = Utils.bannerImages.filter { it -> it.gameId == game.id && it.fileName.contains("banner", ignoreCase = false)}

        holder.binding.tvGamePrice.text = String.format(Locale.US, "$%.2f", game.price)

        Glide.with(holder.itemView.context)
            .load(bannerURL[0].filePath)
            .centerCrop()
            .placeholder(android.R.drawable.ic_menu_gallery)
            .into(holder.binding.imgGameCover)


        holder.binding.btnBuy.setOnClickListener {
            onBuyClick(game)
        }
    }

    override fun getItemCount(): Int = gameList.size

    fun updateList(newGames: List<Game>) {
        gameList = newGames
        notifyDataSetChanged()
    }
}