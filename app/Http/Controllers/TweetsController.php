<?php

namespace App\Http\Controllers;

use App\Tweet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Http\Response;

class TweetsController extends Controller
{
	public function show()
	{
		$connection = new TwitterOAuth(env('CONSUMER_KEY'), env('CONSUMER_SECRET'), env('ACCESS_TOKEN'), env('ACCESS_TOKEN_SECRET'));
		$results = $connection->get('statuses/user_timeline', ['screen_name' => 'realDonaldTrump']);

		foreach ($results as $result) {
			$createdAt = Carbon::parse($result->created_at);

			$existing = Tweet::where('twitter_id', $result->id)->first();

			if (! $existing && $createdAt->isToday()) {
				$tweet = new Tweet();

				$tweet->twitter_id = $result->id;
				$tweet->text = $result->text;
				$tweet->created_at = $createdAt;
				$tweet->updated_at = $createdAt;

				$tweet->save();
			}
		}

		return response()->json(Tweet::where('created_at', '>=', Carbon::today())->get());
    }
}
