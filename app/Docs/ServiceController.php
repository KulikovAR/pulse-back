<?php

namespace App\Docs;

/**
 * @OA\Tag(
 *     name="Services",
 *     description="API Endpoints for Service Management"
 * )
 */
class ServiceController
{
    /**
     * @OA\Get(
     *     path="/api/services",
     *     summary="Get paginated list of services",
     *     tags={"Services"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="ok", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="string", format="uuid"),
     *                     @OA\Property(property="company_id", type="string", format="uuid"),
     *                     @OA\Property(property="name", type="string")
     *                 )),
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="total", type="integer"),
     *                     @OA\Property(property="per_page", type="integer"),
     *                     @OA\Property(property="current_page", type="integer"),
     *                     @OA\Property(property="last_page", type="integer")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="")
     *         )
     *     )
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     *     path="/api/services",
     *     summary="Create a new service",
     *     tags={"Services"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "company_id"},
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="company_id", type="string", format="uuid")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="ok", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="company_id", type="string", format="uuid"),
     *                 @OA\Property(property="name", type="string")
     *             ),
     *             @OA\Property(property="message", type="string", example="Service created successfully")
     *         )
     *     )
     * )
     */
    public function store() {}

    /**
     * @OA\Get(
     *     path="/api/services/{service}",
     *     summary="Get service details",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="service",
     *         in="path",
     *         required=true,
     *         description="Service UUID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="ok", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="company_id", type="string", format="uuid"),
     *                 @OA\Property(property="name", type="string")
     *             ),
     *             @OA\Property(property="message", type="string", example="")
     *         )
     *     )
     * )
     */
    public function show() {}

    /**
     * @OA\Put(
     *     path="/api/services/{service}",
     *     summary="Update service details",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="service",
     *         in="path",
     *         required=true,
     *         description="Service UUID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="company_id", type="string", format="uuid")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="ok", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="company_id", type="string", format="uuid"),
     *                 @OA\Property(property="name", type="string")
     *             ),
     *             @OA\Property(property="message", type="string", example="Service updated successfully")
     *         )
     *     )
     * )
     */
    public function update() {}

    /**
     * @OA\Delete(
     *     path="/api/services/{service}",
     *     summary="Delete a service",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="service",
     *         in="path",
     *         required=true,
     *         description="Service UUID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="ok", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Service deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy() {}
}