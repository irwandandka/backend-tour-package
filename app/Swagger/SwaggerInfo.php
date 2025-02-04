<?php

namespace App\Swagger;

class SwaggerInfo
{
    /**
     * @OA\Info(
     *     title="API Products",
     *     version="1.0.0",
     *     description="API for managing tour package products.",
     *     @OA\Contact(
     *         email="irwndandka@gmail.com"
     *     ),
     *     @OA\License(
     *         name="MIT",
     *         url="https://opensource.org/licenses/MIT"
     *     )
     * )
     * 
     * * @OA\Server(
     *     url="https://apilaravel.irwandandka.my.id",
     *     description="Production server for the API"
     * )
     *
     * @OA\Server(
     *     url="http://localhost:8000",
     *     description="Local development server"
     * )
     */
    public static function init()
    {
        //
    }

    /**
     * @OA\Post(
     *      path="/auth/register",
     *      summary="Register a new user",
     *      tags={"Auth"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","email","password"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="
     *                  [email protected]"),
     *              @OA\Property(property="password", type="string", format="password", example="password"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User registered successfully!",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User registered successfully!"),
     *              @OA\Property(property="user", type="object"),
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *             )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="array", @OA\Items(type="string")),
     *              @OA\Property(property="email", type="array", @OA\Items(type="string")),
     *              @OA\Property(property="password", type="array", @OA\Items(type="string")),
     *          )
     *      )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function register()
    {
        //
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/login",
     *      summary="Login",
     *      description="Login",
     *      operationId="login",
     *      tags={"Auth"},
     *          @OA\RequestBody(
     *              required=true,
     *              description="Login",
     *              @OA\JsonContent(
     *                  required={"email","password"},
     *                  @OA\Property(property="email", type="string", format="email", example="
     *                      [email protected]"),
     *                  @OA\Property(property="password", type="string", format="password", example="password"),
     *              )
     *          ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *              @OA\JsonContent(
     *                  @OA\Property(property="access_token", type="string", example="Bearer token"),
     *                  @OA\Property(property="token_type", type="string", example="Bearer"),
     *              )
     *          ),
     *      @OA\Response(
     *          response=401,
     *          description="Invalid login credentials",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid login credentials"),
     *          )
     *      )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function login()
    {
        //
    }

    /**
     * @OA\Get(
     *      path="/api/v1/user/profile",
     *      summary="Get user profile",
     *      description="Get user profile",
     *      operationId="profile",
     *      tags={"User"},
     *      security={{"sanctum": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="data", type="object"),
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *             )
     *          ) 
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Throwable
     */
    public function profile()
    {
        //
    }

    /**
     * @OA\Get(
     *      path="/auth/google",
     *      summary="Redirect to Google OAuth",
     *      description="Redirect to Google OAuth",
     *      operationId="redirectToGoogle",
     *      tags={"Auth"},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="url",
     *                  type="string",
     *                  example="https://accounts.google.com/o/oauth2/auth?response_type=code&client_id=1234567890&redirect_uri=http%3A%2F%2Flocalhost%3A8000%2Fauth%2Fgoogle%2Fcallback&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile&access_type=offline&approval_prompt=force"
     *              )
     *          )
     *      )
     * )
     * @return \Illuminate\Http\JsonResponse
     * @throws Throwable
     */
    public function redirectToGoogle()
    {
        //
    }

    /**
     * @OA\Get(
     *      path="/auth/google/callback",
     *      summary="Google OAuth Callback",
     *      description="Google OAuth Callback",
     *      operationId="handleGoogleCallback",
     *      tags={"Auth"},
     *      @OA\Parameter(
     *          name="code",
     *          in="query",
     *          description="Google OAuth Code",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="token",
     *                  type="string",
     *                  example="2|3d4f5g6h7j8k9l0"
     *              ),
     *              @OA\Property(
     *                  property="user",
     *                  type="object",
     *                  @OA\Property(
     *                      property="id",
     *                      type="integer",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="John Doe"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      example="irwandandka@gmail.com"
     *                  ),
     *                  @OA\Property(
     *                      property="avatar",
     *                      type="string",
     *                      example="https://lh3.googleusercontent.com/a-/AOh14Gj3z4z5"
     *                  )
     *              )
     *          )
     *      )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Throwable
     * @throws \Google\Exception
     * @throws \Google\Service\Exception
     */
    public function handleGoogleCallback()
    {
        //
    }

    /**
     * @OA\Post(
     *      path="/auth/logout",
     *      summary="Logout",
     *      description="Logout",
     *      operationId="logout",
     *      tags={"Auth"},
     *      security={{"sanctum": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Logged out successfully"
     *              )
     *          )
     *      )
     * )
     */
    public function logout()
    {
        //
    }

    /**
     * @OA\Get(
     *      path="/api/v1/base/languages",
     *      tags={"Base"},
     *      summary="Get languages",
     *      description="Get languages",
     *      operationId="languages",
     *      security={{"bearerAuth": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="name", type="string", example="English"),
     *                      @OA\Property(property="code", type="string", example="en"),
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Throwable
     */
    public function languages()
    {
        //
    }

    /**
     * @OA\Get(
     *      path="/api/v1/base/currencies",
     *      tags={"Base"},
     *      summary="Get currencies",
     *      description="Get currencies",
     *      operationId="currencies",
     *      security={{"bearerAuth": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="US Dollar"),
     *                      @OA\Property(property="code", type="string", example="USD"),
     *                      @OA\Property(property="symbol", type="string", example="$"),
     *                      @OA\Property(property="exchange_rate", type="number", example=1.0),
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Throwable
     */
    public function currencies()
    {
        //
    }

    /**
     * @OA\Get(
     *      path="/api/cities",
     *      summary="List all cities",
     *      description="List all cities",
     *      operationId="listCities",
     *      tags={"Cities"},
     *      @OA\Response(
     *          response=200,
     *          description="List of cities",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="country", type="string"),
     *                  @OA\Property(property="region", type="string"),
     *              )
     *          )
     *      )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Throwable
     */
    public function cities()
    {
        //
    }

    /**
     * @OA\Get(
     *      path="/api/cities/{city}",
     *      summary="Get city by ID",
     *      description="Get city by ID",
     *      operationId="showCity",
     *      tags={"Cities"},
     *      @OA\Parameter(
     *          name="city",
     *          in="path",
     *          required=true,
     *          description="City ID",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="City detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer"),
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="country", type="string"),
     *              @OA\Property(property="region", type="string"),
     *          )
     *      )
     * )
     * @param City $city
     * @return \Illuminate\Http\JsonResponse
     * @throws Throwable
     */
    public function showCity()
    {
        //
    }

    /**
     * @OA\Delete(
     *    path="/api/cities/{city}",
     *   summary="Delete city by ID",
     *  description="Delete city by ID",
     * operationId="deleteCity",
     * tags={"Cities"},
     * @OA\Parameter(
     *    name="city",
     *  in="path",
     * required=true,
     * description="City ID",
     * @OA\Schema(
     *   type="integer"
     * )
     * ),
     * @OA\Response(
     *   response=200,
     * description="City deleted successfully",
     * @OA\JsonContent(
     *  @OA\Property(property="status", type="string"),
     * @OA\Property(property="message", type="string"),
     * )
     * )
     * )
     * @param City $city
     * @return \Illuminate\Http\JsonResponse
     * @throws Throwable
     */
    public function deleteCity()
    {
        //
    }

    /**
     * @OA\Get(
     *      path="/api/cities/deleted",
     *      summary="List all deleted cities",
     *      description="List all deleted cities",
     *      operationId="getDeletedCities",
     *      tags={"Cities"},
     *      @OA\Response(
     *          response=200,
     *          description="List of deleted cities",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="country", type="string"),
     *                  @OA\Property(property="region", type="string"),
     *                  @OA\Property(property="deleted_at", type="string"),
     *              )
     *          )
     *      )
     * )
     * @return \Illuminate\Http\JsonResponse
     * @throws Throwable
     */
    public function getDeletedCities()
    {
        //
    }

    /**
     * @OA\Get(
     *      path="/api/regions",
     *      summary="List all regions",
     *      description="List all regions",
     *      operationId="list",
     *      tags={"Regions"},
     *      @OA\Response(
     *          response=200,
     *          description="List of regions",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="name", type="string", example="Asia"),
     *                  @OA\Property(property="code", type="string", example="AS"),
     *              )
     *          )
     *      )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Throwable
     */
    public function regions()
    {
        //
    }

    /**
     * @OA\Get(
     *      path="/api/regions/{region}",
     *      summary="Show region",
     *      description="Show region",
     *      operationId="show",
     *      tags={"Regions"},
     *      @OA\Parameter(
     *          name="region",
     *          in="path",
     *          required=true,
     *          description="Region ID",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Region detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer", example="1"),
     *              @OA\Property(property="name", type="string", example="Asia"),
     *              @OA\Property(property="code", type="string", example="AS"),
     *              @OA\Property(property="countries", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example="1"),
     *                      @OA\Property(property="name", type="string", example="Indonesia"),
     *                      @OA\Property(property="iso_code", type="string", example="ID"),
     *                      @OA\Property(property="phone_code", type="string", example="62"),
     *                  )
     *              )
     *          )
     *      )
     * )
     * @param Region $region
     * @return \Illuminate\Http\JsonResponse
     * @throws Throwable
     */
    public function regionShow()
    {
        //
    }

    /**
     * @OA\GET(
     *      path="/api/v1/product/explore-now",
     *      tags={"Product"},
     *      summary="Retrieve a list of cities.",
     *      description="Returns a list of cities which has the highest reviews of their tour packages.",
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved the list of cities",
     *          @OA\JsonContent(
     *              type="object"
     *          )
     *      )
     * )
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public static function exploreNow()
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/v1/product/list",
     *     tags={"Product"},
     *     summary="Retrieve a list of all tour packages",
     *     description="Returns a list of available tour packages.",
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved the list of products",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="List of tour packages.",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="string",
     *                         format="uuid",
     *                         example="0c40bf88-ba9f-11ef-95b1-525400d81c3e",
     *                         description="Unique identifier for the tour package."
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Paris Art & Culture",
     *                         description="The name of the tour package."
     *                     ),
     *                     @OA\Property(
     *                         property="slug",
     *                         type="string",
     *                         example="paris-art-&-culture",
     *                         description="URL-friendly version of the package name."
     *                     ),
     *                     @OA\Property(
     *                         property="duration",
     *                         type="string",
     *                         example="2 days",
     *                         description="Duration of the tour package."
     *                     ),
     *                     @OA\Property(
     *                         property="description",
     *                         type="string",
     *                         example="Dive into the artistic and cultural history of Paris.",
     *                         description="Description of the tour package."
     *                     ),
     *                     @OA\Property(
     *                         property="price",
     *                         type="integer",
     *                         example=5000000,
     *                         description="Price of the tour package in the smallest currency unit."
     *                     ),
     *                     @OA\Property(
     *                         property="capacity",
     *                         type="integer",
     *                         nullable=true,
     *                         example=null,
     *                         description="Maximum number of participants. Can be null if not specified."
     *                     ),
     *                     @OA\Property(
     *                         property="date_from",
     *                         type="string",
     *                         format="date",
     *                         example="2024-12-12",
     *                         description="Start date of the tour package."
     *                     ),
     *                     @OA\Property(
     *                         property="date_until",
     *                         type="string",
     *                         format="date",
     *                         example="2025-03-12",
     *                         description="End date of the tour package."
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error."
     *     )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public static function list()
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/v1/product/{slug}",
     *     tags={"Product"},
     *     summary="Get a single tour package by slug",
     *     description="Retrieve detailed information about a specific tour package using the package's slug.",
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="The slug of the tour package",
     *         @OA\Schema(
     *             type="string",
     *             example="tokyo-highlights"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved the product details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success",
     *                 description="Response status."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="The detailed information about the tour package.",
     *                 @OA\Property(
     *                     property="id",
     *                     type="string",
     *                     format="uuid",
     *                     example="6060c477-ba9e-11ef-95b1-525400d81c3e",
     *                     description="The unique identifier of the product."
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="Tokyo Highlights",
     *                     description="The name of the tour package."
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Experience the top attractions and activities in Tokyo.",
     *                     description="A description of the tour package."
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     nullable=true,
     *                     example=null,
     *                     description="The URL of the tour package's image. Can be null if no image is available."
     *                 ),
     *                 @OA\Property(
     *                     property="duration",
     *                     type="string",
     *                     example="3 days",
     *                     description="Duration of the tour package."
     *                 ),
     *                 @OA\Property(
     *                     property="city",
     *                     type="string",
     *                     example="Tokyo",
     *                     description="City where the tour takes place."
     *                 ),
     *                 @OA\Property(
     *                     property="country",
     *                     type="string",
     *                     example="Japan",
     *                     description="Country where the tour takes place."
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="integer",
     *                     example=6000000,
     *                     description="Price of the tour package in the smallest currency unit."
     *                 ),
     *                 @OA\Property(
     *                     property="rating",
     *                     type="number",
     *                     format="float",
     *                     example=4.7,
     *                     description="Rating of the tour package."
     *                 ),
     *                 @OA\Property(
     *                     property="reviews",
     *                     type="array",
     *                     description="List of reviews for the tour package.",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="string",
     *                             format="uuid",
     *                             example="46aab2c9-a2ff-45a3-a970-038fd65b3484",
     *                             description="Review identifier."
     *                         ),
     *                         @OA\Property(
     *                             property="user",
     *                             type="string",
     *                             example="Irwanda Andika Putra",
     *                             description="Name of the reviewer."
     *                         ),
     *                         @OA\Property(
     *                             property="rating",
     *                             type="integer",
     *                             example=5,
     *                             description="Rating given by the reviewer."
     *                         ),
     *                         @OA\Property(
     *                             property="comment",
     *                             type="string",
     *                             example="Absolutely loved it! The mix of traditional and modern Tokyo was amazing. Would definitely book again.",
     *                             description="Comment provided by the reviewer."
     *                         ),
     *                         @OA\Property(
     *                             property="review_date",
     *                             type="string",
     *                             format="date-time",
     *                             example="2024-02-07 04:26:58",
     *                             description="Date when the review was posted."
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="product_details",
     *                     type="array",
     *                     description="List of product details for each day of the tour.",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="string",
     *                             format="uuid",
     *                             example="1d54cd38-baab-11ef-95b1-525400d81c3e",
     *                             description="Product detail identifier."
     *                         ),
     *                         @OA\Property(
     *                             property="day",
     *                             type="string",
     *                             example="Day 1",
     *                             description="The day of the tour."
     *                         ),
     *                         @OA\Property(
     *                             property="title",
     *                             type="string",
     *                             example="Landmark Exploration",
     *                             description="Title of the activity on this day."
     *                         ),
     *                         @OA\Property(
     *                             property="schedule_time",
     *                             type="string",
     *                             example="09:00",
     *                             description="Time scheduled for the activity."
     *                         ),
     *                         @OA\Property(
     *                             property="image",
     *                             type="string",
     *                             nullable=true,
     *                             example=null,
     *                             description="Image URL for the activity. Can be null."
     *                         ),
     *                         @OA\Property(
     *                             property="description",
     *                             type="string",
     *                             example="Visit iconic landmarks including the Tokyo Tower.",
     *                             description="Description of the activity."
     *                         ),
     *                         @OA\Property(
     *                             property="latitude",
     *                             type="string",
     *                             example="35.65860000",
     *                             description="Latitude of the activity's location."
     *                         ),
     *                         @OA\Property(
     *                             property="longitude",
     *                             type="string",
     *                             example="139.74540000",
     *                             description="Longitude of the activity's location."
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error."
     *     )
     * )
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public static function show()
    {
        //
    }

    /**
     * @OA\Get(
     *      path="/product/popular-destination",
     *      summary="Get popular destinations",
     *      tags={"Product"},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  example="success"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(
     *                          property="id",
     *                          type="integer",
     *                          example=1
     *                      ),
     *                      @OA\Property(
     *                          property="name",
     *                          type="string",
     *                          example="Bali"
     *                      ),
     *                      @OA\Property(
     *                          property="slug",
     *                          type="string",
     *                          example="bali"
     *                      ),
     *                      @OA\Property(
     *                          property="price",
     *                          type="integer",
     *                          example=1000000
     *                      ),
     *                      @OA\Property(
     *                          property="rating",
     *                          type="number",
     *                          format="float",
     *                          example=4.5
     *                      )
     *                  )
     *              )
     *          )
     *      )
     * )
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public static function popularDestination()
    {
        //
    }
}
