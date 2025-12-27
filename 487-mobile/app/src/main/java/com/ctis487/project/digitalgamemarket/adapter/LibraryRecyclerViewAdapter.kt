package com.ctis487.project.digitalgamemarket.adapter

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.ctis487.project.digitalgamemarket.R
import com.ctis487.project.digitalgamemarket.model.Checkout
import com.ctis487.project.digitalgamemarket.model.Game


class LibraryRecyclerViewAdapter(private val context: Context, private var recyclerItemValues: List<Checkout>) :
    RecyclerView.Adapter<LibraryRecyclerViewAdapter.RecyclerViewItemHolder>() {
    private lateinit var games: MutableList<Game>

    fun setData(items : List<Checkout>, gameslist : MutableList<Game>){
        recyclerItemValues = items
        games = gameslist
        notifyDataSetChanged()
    }

    override fun onCreateViewHolder(viewGroup: ViewGroup, viewType: Int): RecyclerViewItemHolder {
        val inflator = LayoutInflater.from(viewGroup.context)
        val itemView: View = inflator.inflate(R.layout.library_item_layout, viewGroup, false)
        return RecyclerViewItemHolder(itemView)
    }

    override fun onBindViewHolder(myRecyclerViewItemHolder: RecyclerViewItemHolder, position: Int) {
        val item = recyclerItemValues[position]

        myRecyclerViewItemHolder.tvItemBoughtDate.text=item.date



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
