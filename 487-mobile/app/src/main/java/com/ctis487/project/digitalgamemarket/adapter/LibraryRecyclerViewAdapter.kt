package com.ctis487.project.digitalgamemarket.adapter

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.ctis487.project.digitalgamemarket.R
import com.ctis487.project.digitalgamemarket.model.*


class LibraryRecyclerViewAdapter(private val context: Context, private var recyclerItemValues: List<LibItem>) :
    RecyclerView.Adapter<LibraryRecyclerViewAdapter.RecyclerViewItemHolder>() {

    fun setData(items : List<LibItem>){
        recyclerItemValues = items
        notifyDataSetChanged()
    }

    override fun onCreateViewHolder(viewGroup: ViewGroup, viewType: Int): RecyclerViewItemHolder {
        val inflator = LayoutInflater.from(viewGroup.context)
        val itemView: View = inflator.inflate(R.layout.library_item_layout, viewGroup, false)
        return RecyclerViewItemHolder(itemView)
    }

    override fun onBindViewHolder(myRecyclerViewItemHolder: RecyclerViewItemHolder, position: Int) {
        val item = recyclerItemValues[position]

        myRecyclerViewItemHolder.tvItemBoughtDate.text= item.libCheckOut.date
        myRecyclerViewItemHolder.tvItemName.text = item.libGame.name

        Glide.with(context)
            .load(item.libGame.logoPath)
            .placeholder(R.drawable.ic_launcher_foreground)
            .error(R.drawable.ic_launcher_foreground)
            .centerCrop()
            .into(myRecyclerViewItemHolder.tvItemImg)


        /*
        myRecyclerViewItemHolder.tvItemCustomerId.text = item.id.toString()
        myRecyclerViewItemHolder.tvItemCustomerName.text = item.name
        myRecyclerViewItemHolder.tvItemCustomerQuantity.text = item.surname + ""
        */
    }


    override fun getItemCount(): Int {
        return recyclerItemValues.size
    }

    inner class RecyclerViewItemHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        lateinit var parentLayout: LinearLayout
        lateinit var tvItemName: TextView
        lateinit var tvItemImg: ImageView
        lateinit var tvItemBoughtDate: TextView
        init {
            parentLayout = itemView.findViewById(R.id.itemLayout)
            tvItemName = itemView.findViewById(R.id.gameNametv)
            tvItemImg = itemView.findViewById(R.id.gameLogoImg)
            tvItemBoughtDate = itemView.findViewById(R.id.BoughtDateTv)
        }
    }


}
