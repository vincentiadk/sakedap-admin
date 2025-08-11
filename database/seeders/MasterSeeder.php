<?php

use Illuminate\Database\Seeder;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Migrating ' . DB::connection('backup')->table('meta_subjects')->count() . " meta_subjects table");
        $subjects = DB::connection('backup')->table('meta_subjects')->get();
        foreach ($subjects as $subject) {
            $created_by = 1;
            if ($subject->created_by != "") {
                if ($user =  App\Models\User::where('username', $subject->created_by)->first()) {
                    $created_by  = $user->id;
                }
            }
            $subjectId = App\Models\Subject::insertGetId([
                'name'       => $subject->name_subject,
                'slug'       => \Str::slug($subject->name_subject),
                'created_at' => $subject->created_at,
                'updated_at' => $subject->created_at,
            ]);
            if ($created_by != 1) {
                App\Models\ActivityLog::insert([
                    'log_name'          => "Create Subjects",
                    'description'       => json_encode([
                        'name'       => $subject->name_subject,
                        'slug'       => \Str::slug($subject->name_subject),
                    ]),
                    'subject_id'        => $subjectId,
                    'subject_type'      => 'App\Models\Subject',
                    'causer_id'         => $created_by,
                    'causer_type'       => 'App\Models\User',
                    'properties'        => "[]",
                    'created_at'        => $subject->created_at,
                    'updated_at'        => $subject->created_at,
                ]);
            }
        }
        $this->command->info('Migrating meta_subjects table finished. Total subjects = ' . App\Models\Subject::count());

        $this->command->info('Migrating ' . DB::connection('backup')->table('meta_contributor')->count() . " meta_contributor table");
        $contributors = DB::connection('backup')->table('meta_contributor')->get();
        foreach ($contributors as $contributor) {
            App\Models\Contributor::insert([
                'name' => $contributor->display_contributor,
                'type' => $contributor->id_type,
                'slug' => \Str::slug($contributor->display_contributor),
                'created_at' => $contributor->created_at,
                'updated_at' => $contributor->updated_at,
            ]);
        }
        $this->command->info('Migrating meta_contributor table finished. Total contributors = ' . App\Models\Contributor::count());

        $this->command->info('Migrating ' . DB::connection('backup')->table('genres')->count() . " genres table to categories");
        $genres = DB::connection('backup')->table('genres')->get();
        foreach ($genres as $genre) {
            App\Models\Category::insert([
                'name' => $genre->name,
                'type' => 5,
                'slug' => \Str::slug($genre->name),
                'created_at' => $genre->created_at,
                'updated_at' => $genre->created_at,
            ]);
        }
        $this->command->info('Migrating genres to categories table finished. Total categories = ' . App\Models\Category::count());

        $this->command->info('Migrating ' . DB::connection('backup')->table('contents')->count() . " contents table to categories");
        $contents = DB::connection('backup')->table('contents')->get();
        foreach ($contents as $content) {
            App\Models\Category::insert([
                'name' => $content->name,
                'type' => 6,
                'slug' => \Str::slug($content->name),
                'created_at' => $content->created_at,
                'updated_at' => $content->created_at,
            ]);
        }
        $this->command->info('Migrating contents to categories table finished. Total categories = ' . App\Models\Category::count());
    }
}
