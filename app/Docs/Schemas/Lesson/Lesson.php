<?php 

namespace App\Docs\Schemas\Lesson;

/**
 * @OA\Schema(
 *     title="Lesson",
 *     description="Lesson model",
 *     @OA\Xml(
 *         name="Lesson"
 *     )
 * )
 */

class Lesson {
    /**
     * @OA\Property(
     *     title="id",
     *     description="Id урока",
     *     format="string",
     *     example="99cf5f65-ad91-4b43-9cb7-88088e92ea68"
     * )
     *
     * @var string
     */
    private $id;

    /**
     * @OA\Property(
     *     title="title",
     *     description="Название урока",
     *     format="string",
     *     example="Новый урок"
     * )
     *
     * @var string
     */
    private $title;

    /**
     * @OA\Property(
     *     title="description",
     *     description="Описание урока",
     *     format="string",
     *     example="В этом уроке мы рассмотрим графический дизайн, который включает создание логотипов, иллюстраций, макетов и других графических элементов. Вы узнаете о принципах композиции, цветовой теории и использовании графических инструментов."
     * )
     *
     * @var string
     */
    private $description;


    /**
     * @OA\Property(
     *     title="description",
     *     description="Пусть к видео урока",
     *     format="string",
     *     example="https://trueschool/video/design.mp4"
     * )
     *
     * @var string
     */
    private $video_path;

    /**
     * @OA\Property(
     *     title="description",
     *     description="Пусть к видео урока",
     *     format="string",
     *     example="https://trueschool/previes/design.png"
     * )
     *
     * @var string
     */
    private $preview_path;

    /**
     * @OA\Property(
     *     title="description",
     *     description="Дата анонса",
     *     format="string",
     *     example="05.04.2023"
     * )
     *
     * @var string
     */
    private $announc_date;
}