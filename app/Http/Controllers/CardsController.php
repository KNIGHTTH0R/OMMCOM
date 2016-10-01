<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Card;
class CardsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $card = Card::all();
        //return $card;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $card = new Card;
        return view('cards.create', ['card' => $card ]);  
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd(\Input::all());
        //dd($request->all());
        //$card = new Card();
        $data = \Input::all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $card = Card::find($data['id']);
        }else{
            $card = new Card();
        }

       //$card->create(\Input::all());
        $card->fill(\Input::all())->save();
        //$card->update(\Input::all());
        //$card->save();
        //$card->create($request->all());
        //return back();
        return redirect('create');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $card = Card::findOrFail($id);

        return view('cards.create', ['card' => $card ]);   
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Card $card)
    {

        return $card;
        //$card = Card::find($card);
        //return $card;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
