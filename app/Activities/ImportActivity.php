<?php

namespace App\Activities;

class ImportActivity {
    public function importFromHistory() {
        // extract kids
        $progress = $this->importProgress();
        $this->importLevels();
        $this->importSubjects();
        $this->importParents();
        $this->importKids();

        \App\Models\ExtractedHistory::truncate();
        return $progress;
    }


    public function importProgress() {
        $already_exists = \DB::table('extracted_histories')
            ->select('extracted_histories.id')
            ->join('progress', function($join) {
                $join->on('extracted_histories.kid_id', 'progress.kid_id');
                $join->on('extracted_histories.subject_id', 'progress.subject_id');
                $join->on('extracted_histories.date', 'progress.date');
            });

        $idsThatMatters = \App\Models\ExtractedHistory::selectRaw('max(id) as id')
            ->whereNotIn('id', $already_exists )
            ->groupBy( 'kid_id', 'subject_id', 'date' );

        $works = \App\Models\ExtractedHistory::select(
                'date',
                'parent',
                'kid_id',
                'kid_name',
                'level',
                'subject_id',
                'subject',
                'type',
                'unit',
                'section',
                'duration',
                'score',
                'link',
            )
            ->whereIn('id', $idsThatMatters )
            ->get()->toArray();
        
        if( count($works) > 0) $this->setRecords(new \App\Models\Progress(), $works);

        return $works;
    }

    public function importLevels() {
        $levels = \App\Models\ExtractedHistory::select('level as name')
            ->whereNotIn('level', \App\Models\Level::select('name'))
            ->groupBy('level')
            ->get()->toArray();

        if( count($levels) > 0) $this->setRecords(new \App\Models\Level(), $levels);
    }

    public function importSubjects() {
        $subjects = \App\Models\ExtractedHistory::select('subject_id', 'subject as name')
            ->whereNotIn('subject_id', \App\Models\Subject::select('id'))
            ->groupBy('subject_id', 'subject')
            ->get()->toArray();

        if( count($subjects) > 0) $this->setRecords(new \App\Models\Subject(), $subjects);
    }

    public function importParents() {
        $parents = \App\Models\ExtractedHistory::select('parent as name')
            ->whereNotIn('parent', \App\Models\ParentAcc::select('name'))
            ->groupBy('parent')
            ->get()->toArray();

        if( count($parents) > 0) $this->setRecords(new \App\Models\ParentAcc(), $parents);
    }

    public function importKids() {
        $kids = \App\Models\ExtractedHistory::select('kid_id as id', 'kid_name as name')
            ->whereNotIn('kid_id', \App\Models\Kid::select('id'))
            ->groupBy('kid_id','kid_name')
            ->get()->toArray();

        if( count($kids) > 0 ) $this->setRecords(new \App\Models\Kid(), $kids);
    }

    public function setRecords($model, $data){
        $now = \Carbon\Carbon::now();
        $model->insert($data);
        $model->where('created_at',null)->update([ 'created_at' => $now, 'updated_at' => $now ]);
    }
}