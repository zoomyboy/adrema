<?php

namespace Tests\Feature\Form;

use App\Form\Data\ExportData;
use App\Lib\Editor\Condition;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\RequestFactories\EditorRequestFactory;
use Worksome\RequestFactories\RequestFactory;

/**
 * @method self name(string $name)
 * @method self from(string $date)
 * @method self to(string $date)
 * @method self description(?EditorRequestFactory $description)
 * @method self mailTop(?EditorRequestFactory $content)
 * @method self mailBottom(?EditorRequestFactory $content)
 * @method self excerpt(string $description)
 * @method self registrationFrom(string|null $date)
 * @method self registrationUntil(string|null $date)
 * @method self isActive(bool $isActive)
 * @method self isPrivate(bool $isPrivate)
 * @method self export(ExportData $export)
 * @method self preventionText(EditorRequestFactory $text)
 */
class FormRequest extends RequestFactory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(4, true),
            'description' => [
                'time' => 45069432,
                'blocks' => [
                    ['id' => 'TTzz66', 'type' => 'paragraph', 'data' => ['text' => 'lorem']]
                ],
                'version' => '1.0',
            ],
            'excerpt' => $this->faker->words(10, true),
            'config' => ['sections' => []],
            'from' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'to' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'registration_from' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'registration_until' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'mail_top' => EditorRequestFactory::new()->create(),
            'mail_bottom' => EditorRequestFactory::new()->create(),
            'header_image' => $this->getHeaderImagePayload(str()->uuid() . '.jpg'),
            'mailattachments' => [],
            'export' => ExportData::from([])->toArray(),
            'needs_prevention' => $this->faker->boolean(),
            'prevention_text' => EditorRequestFactory::new()->create(),
            'prevention_conditions' => Condition::defaults(),
        ];
    }

    /**
     * @param array<int, FormtemplateSectionRequest> $sections
     */
    public function sections(array $sections): self
    {
        return $this->state(['config.sections' => $sections]);
    }

    /**
     * @param mixed $args
     */
    public function __call(string $method, $args): self
    {
        return $this->state([str($method)->snake()->toString() => $args[0]]);
    }

    public function headerImage(string $fileName): self
    {
        UploadedFile::fake()->image($fileName, 1000, 1000)->storeAs('media-library', $fileName, 'temp');

        Storage::disk('temp')->assertExists('media-library/' . $fileName);

        return $this->state([
            'header_image' => $this->getHeaderImagePayload($fileName)
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function getHeaderImagePayload(string $fileName): array
    {
        UploadedFile::fake()->image($fileName, 1000, 1000)->storeAs('media-library', $fileName, 'temp');

        Storage::disk('temp')->assertExists('media-library/' . $fileName);

        return [
            'file_name' => $fileName,
            'collection_name' => 'headerImage',
        ];
    }
}
