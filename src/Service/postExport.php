<?php


namespace App\Service;


use App\Repository\PostRepository;

class postExport
{
    public function export(PostRepository $postRepository)
    {
        $postValue = $postRepository->findAll();
        foreach ($postValue as $post) {
            $categories = [];
            foreach ($post->getCategories() as $category) {
                $categories[] = $category->getName();
            }
            $posts[] = [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'date' => $post->getCreated()->format('Y-m-d'),
                'subheadline' => $post->getSubheadline(),
                'description' => $post->getDescription(),
                'categories' => $categories,
            ];
            $response['posts'] = $posts;
            $fp = fopen('exportPost.json', 'w');
            fwrite($fp, (json_encode($response)));
            fclose($fp);
        }

    }

    function file_force_download($file)
    {
        if (file_exists($file)) {

            if (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
    }

}