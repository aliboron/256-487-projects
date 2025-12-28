package com.ctis487.project.digitalgamemarket.adapter

import android.graphics.drawable.AnimationDrawable
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.bumptech.glide.util.Util
import com.ctis487.project.digitalgamemarket.R
import com.ctis487.project.digitalgamemarket.databinding.DiscountedStoreGameItemBinding
import com.ctis487.project.digitalgamemarket.databinding.StoreGameItemBinding
import com.ctis487.project.digitalgamemarket.db.Utils
import com.ctis487.project.digitalgamemarket.model.Game
import java.util.Locale
import kotlin.random.Random
import kotlin.random.nextInt

class StoreGamesAdapter(
    var gameList: List<Game>,
    private val onBuyClick: (Game, Double) -> Unit
) : RecyclerView.Adapter<RecyclerView.ViewHolder>() {

    inner class StoreGameViewHolder(val binding: StoreGameItemBinding) :
        RecyclerView.ViewHolder(binding.root)

    inner class DiscountedStoreGameViewHolder(val binding: DiscountedStoreGameItemBinding) :
        RecyclerView.ViewHolder(binding.root)

    lateinit var gameDiscountHolder: MutableList<Int>

    companion object {
        const val DISCOUNTED = 0
        const val BASE_PRICE = 1
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): RecyclerView.ViewHolder {
        if (viewType == BASE_PRICE) {
            val binding =
                StoreGameItemBinding.inflate(LayoutInflater.from(parent.context), parent, false)
            return StoreGameViewHolder(binding)
        } else {
            val binding = DiscountedStoreGameItemBinding.inflate(
                LayoutInflater.from(parent.context),
                parent,
                false
            )
            return DiscountedStoreGameViewHolder(binding)
        }
    }

    override fun onBindViewHolder(holder: RecyclerView.ViewHolder, position: Int) {
        if (getItemViewType(position) == BASE_PRICE){
            val game = gameList[position]
            val itemHolder = holder as StoreGameViewHolder
            itemHolder.binding.tvGameName.text = game.name
            itemHolder.binding.tvGameGenre.text = game.genre

            val bannerURL = Utils.bannerImages.filter { it ->
                it.gameId == game.id && it.fileName.contains(
                    "banner",
                    ignoreCase = false
                )
            }

            itemHolder.binding.tvGamePrice.text = String.format(Locale.US, "$%.2f", game.price)

            Glide.with(itemHolder.itemView.context)
                .load(bannerURL[0].filePath)
                .centerCrop()
                .placeholder(android.R.drawable.ic_menu_gallery)
                .into(itemHolder.binding.imgGameCover)


            itemHolder.binding.btnBuy.setOnClickListener {
                onBuyClick(game, game.price)
            }
        } else
        {
            val game = gameList[position]
            val itemHolder = holder as DiscountedStoreGameViewHolder
            itemHolder.binding.tvGameName.text = game.name
            itemHolder.binding.tvGameGenre.text = game.genre

            val bannerURL = Utils.bannerImages.filter { it ->
                it.gameId == game.id && it.fileName.contains(
                    "banner",
                    ignoreCase = false
                )
            }
            var discPrice = game.price * 0.8
            itemHolder.binding.tvGamePrice.text = String.format(Locale.US, "$%.2f", discPrice)

            Glide.with(itemHolder.itemView.context)
                .load(bannerURL[0].filePath)
                .centerCrop()
                .placeholder(android.R.drawable.ic_menu_gallery)
                .into(itemHolder.binding.imgGameCover)


            itemHolder.binding.btnBuy.setOnClickListener {
                onBuyClick(game, discPrice)
            }
            var container = itemHolder.binding.discountedStoreItemLayout
            container.setBackgroundResource(R.drawable.anim_gold_shine)

            val animationDrawable = container.background as? AnimationDrawable
            animationDrawable?.setEnterFadeDuration(1000)
            animationDrawable?.setExitFadeDuration(1000)
            animationDrawable?.start()

        }
    }

    override fun getItemCount(): Int = gameList.size

    override fun getItemViewType(position: Int): Int {
        return gameDiscountHolder[position]
    }

    fun updateList(newGames: List<Game>) {
        gameList = newGames
        gameDiscountHolder = MutableList<Int>(gameList.size) { i ->
            val roll = Random.nextInt(1..10)
            if (roll <= 3) DISCOUNTED else BASE_PRICE
        }
        notifyDataSetChanged()
    }

}